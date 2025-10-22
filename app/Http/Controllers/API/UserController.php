<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Hariri Foundation API",
     *      description="API documentation for Hariri Foundation project",
     *      @OA\Contact(email="support@haririfoundation.com")
     * )
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users or by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Optional user ID to fetch a specific user",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Users retrieved successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('id')) {
            $user = User::find($request->id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user);
        }

        $users = User::all();
        return response()->json([
            'data' => $users,
            'message' => 'Users retrieved successfully'
        ]);
    }
}
