document.addEventListener("DOMContentLoaded", function () {
    // ---------- SELECT2 INITIALIZATION ----------

    $("#programs_select").select2({
        placeholder: "Select a program...",
        allowClear: true,
        width: "100%",
        minimumResultsForSearch: 10,
    });

    $("#projects_select").select2({
        placeholder: "Select projects...",
        allowClear: true,
        width: "100%",
        closeOnSelect: false,
        multiple: true,
    });

    $("#rp_component_id").select2({
        placeholder: "Select a reporting component...",
        allowClear: true,
        width: "100%",
    });

    $("#rp_activities_select").select2({
        placeholder: "Select reporting activities...",
        allowClear: true,
        width: "100%",
        closeOnSelect: false,
        multiple: true,
    });

    
// Initialize focal points select
$('#focal_points_select').select2({
    placeholder: 'Select focal points...',
    allowClear: true,
    width: '100%',
    closeOnSelect: false,
    multiple: true
});

    // ---------- ROUTES & DATA FROM BLADE ----------

    const routes = window.activityRoutes;
    const selectedProjects = window.selectedProjects || [];
    const selectedRpActivities = window.selectedRpActivities || [];

    // ---------- PROJECTS BY PROGRAM ----------

    function loadProjectsByProgram(programId) {
        const projectsSelect = $("#projects_select");

        if (!programId) {
            projectsSelect.empty().trigger("change");
            return;
        }

        projectsSelect.html("<option>Loading...</option>").trigger("change");

        $.ajax({
            url: routes.projectsByProgram,
            method: "GET",
            data: { program_id: programId },
            dataType: "json",
            success: function (response) {
                projectsSelect.empty();

                if (response.success && response.projects) {
                    response.projects.forEach((project) => {
                        projectsSelect.append(
                            $("<option>")
                                .val(project.project_id)
                                .text(project.name)
                        );
                    });

                    if (selectedProjects.length) {
                        projectsSelect.val(selectedProjects).trigger("change");
                    }
                }
            },
        });
    }

    $("#programs_select").on("change", function () {
        loadProjectsByProgram($(this).val());
    });

    // ---------- RP ACTIVITIES ----------

    function loadRPActivities(componentId) {
        const select = $("#rp_activities_select");

        if (!componentId) return;

        select.html("<option>Loading...</option>").trigger("change");

        $.ajax({
            url: routes.rpActivities,
            method: "GET",
            data: { component_id: componentId },
            dataType: "json",
            success: function (response) {
                select.empty();

                if (response.success && response.data) {
                    response.data.forEach((action) => {
                        const optgroup = $("<optgroup>").attr(
                            "label",
                            `${action.action_code} - ${action.action_name}`
                        );

                        action.activities.forEach((activity) => {
                            optgroup.append(
                                $("<option>")
                                    .val(activity.rp_activities_id)
                                    .text(`${activity.code} - ${activity.name}`)
                            );
                        });

                        select.append(optgroup);
                    });

                    if (selectedRpActivities.length) {
                        select.val(selectedRpActivities).trigger("change");
                    }
                }
            },
        });
    }

    $("#rp_component_id").on("change", function () {
        loadRPActivities($(this).val());
    });
});
