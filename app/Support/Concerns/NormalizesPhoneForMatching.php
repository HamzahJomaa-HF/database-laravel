<?php

namespace App\Support\Concerns;

use App\Models\User;

/**
 * Shared phone-matching logic for the bulk importers (ActivityUserController,
 * FinancialsImport). Phone numbers on file show up in several equivalent
 * formats — 96181968927, 81968927, 9613098741, 03098741, 3098741 — that all
 * refer to the same subscriber once the 961 country code and any leading
 * trunk zero are stripped. Matching on the raw stored string (as the plain
 * `where('phone_number', ...)` lookups used to) misses those and creates
 * duplicate users; matching on the normalized core catches them.
 */
trait NormalizesPhoneForMatching
{
    /** @var array<string, string>|null normalized core => user_id */
    private ?array $phoneMatchIndex = null;

    protected function normalizePhoneCore(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '' || $digits === null) {
            return null;
        }

        // Strip the international access code (00), the 961 country code, and
        // any leading trunk zero — in whatever order/combination they show up
        // (961XXXXXXXX, 0961XXXXXXXX, 00961XXXXXXXX, 0XXXXXXX, 961 0XXXXXXX)
        // — until only the bare subscriber number is left. A single pass in a
        // fixed order misses cases like a stray 0 typed before the country
        // code, or one left between the country code and the number.
        for ($i = 0; $i < 4 && strlen($digits) > 4; $i++) {
            if (str_starts_with($digits, '00')) {
                $digits = substr($digits, 2);
                continue;
            }
            if (str_starts_with($digits, '961')) {
                $digits = substr($digits, 3);
                continue;
            }
            if (str_starts_with($digits, '0')) {
                $digits = substr($digits, 1);
                continue;
            }
            break;
        }

        return $digits !== '' ? $digits : null;
    }

    /**
     * Looks up a user by normalized phone core using a lazily-built, per-run
     * index (users.phone_number => normalized core) instead of an exact DB
     * match, so equivalent formats resolve to the same person regardless of
     * which one is on file.
     */
    protected function findUserIdByPhoneCore(?string $phoneCore): ?string
    {
        if ($phoneCore === null) {
            return null;
        }

        if ($this->phoneMatchIndex === null) {
            $this->phoneMatchIndex = [];

            User::whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->select('user_id', 'phone_number')
                ->chunk(1000, function ($users) {
                    foreach ($users as $u) {
                        $core = $this->normalizePhoneCore($u->phone_number);
                        if ($core !== null && !isset($this->phoneMatchIndex[$core])) {
                            $this->phoneMatchIndex[$core] = $u->user_id;
                        }
                    }
                });
        }

        return $this->phoneMatchIndex[$phoneCore] ?? null;
    }

    /**
     * Keeps the lazily-built phone index in sync when a user is created or
     * gets a phone_number filled in mid-run, so a later row in the same
     * import can still find them by phone instead of creating a duplicate.
     * No-op until the index has actually been built once.
     */
    protected function indexUserPhone(?string $userId, ?string $phone): void
    {
        if ($userId === null || $this->phoneMatchIndex === null) {
            return;
        }

        $core = $this->normalizePhoneCore($phone);
        if ($core !== null) {
            $this->phoneMatchIndex[$core] = $userId;
        }
    }
}
