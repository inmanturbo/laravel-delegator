<?php

return [

    /*
     * The connection name to reach the delegator database
     */
    'delegator_database_connection_name' => null,

    'candidates' => [

        // 'team' => [

        //     /*
        //     * This class is responsible for determining which candidate should be current
        //     * for the given request.
        //     *
        //     * This class must implement the `Inmanturbo\Delegator\CandidateFinder\Contracts\CandidateFinder` interface.
        //     *
        //     */
        //     'candidate_finder' => null,

        //     /*
        //     * These fields are used by candidates:artisan command to match one or more tenant
        //     */
        //     'candidate_artisan_search_fields' => [
        //         'id',
        //     ],

        //     /*
        //     * These tasks will be performed when switching candidates.
        //     *
        //     * A valid task is any class that implements the `Inmanturbo\Delegator\Tasks\Contracts\SwitchCandidateTask` interface.
        //     */
        //     'switch_candidate_tasks' => [
        //         \Inmanturbo\Delegator\Tasks\SwitchCandidateConfigTask::class,
        //     ],

        //     /*
        //     * This class is the model used for storing configuration on candidates.
        //     *
        //     * It must implemement the `Inmanturbo\Delegator\Models\Contracts\CandidateModel` interface.
        //     */
        //     'model' => \App\Models\Team::class,

        //     /*
        //     * The connection name to reach the candidate database.
        //     *
        //     * Set to `null` to use the default connection.
        //     */
        //     'candidate_database_connection_name' => null,

        //     /*
        //     * This key will be used to bind the current candidate in the container.
        //     */
        //     'current_candidate_container_key' => 'currentTeam',

        //     /*
        //     * You can customize some of the behavior of this package by using your own custom action.
        //     * Your custom action should always extend the default one.
        //     */
        //     'actions' => [
        //         'make_current_action' => \Inmanturbo\Delegator\Actions\MakeCandidateCurrentAction::class,
        //         'forget_current_action' => \Inmanturbo\Delegator\Actions\ForgetCandidateCurrentAction::class,
        //         'migrate_action' => \Inmanturbo\Delegator\Actions\MigrateCandidateAction::class,
        //     ],
        // ],
    ],
];
