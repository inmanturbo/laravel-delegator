<?php

return [

    /*
     * The connection name to reach the delegator database
     */
    'delegator_database_connection_name' => null,

    // /*
    //  * The candidate currently being used as a tenant.
    //  */
    // 'tenant' => 'team',

    'candidates' => [

        // 'team' => [

        //     /*
        //     * This class is responsible for determining which candidate should be current
        //     * for the given request.
        //     *
        //     * This class must implement the `App\Contracts\CandidateFinder` interface.
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
        //     * A valid task is any class that implements the `App\Contracts\SwitchCandidateTask` interface.
        //     */
        //     'switch_candidate_tasks' => [
        //         \Inmanturbo\Delegator\Tasks\SwitchCandidateConfigTask::class,
        //     ],

        //     /*
        //     * This class is the model used for storing configuration on candidates.
        //     *
        //     * It must implemement the `App\Models\Contracts\CandidateModel` interface.
        //     */
        //     'model' => \App\Models\Team::class,

        //     /*
        //     * If there is a current tenant when dispatching a job, the id of the current tenant
        //     * will be automatically set on the job. When the job is executed, the set
        //     * tenant on the job will be made current.
        //     */
        //     'queues_are_tenant_aware_by_default' => false,

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
        //         'make_queue_tenant_aware_action' => \Inmanturbo\Delegator\Actions\MakeQueueTenantAwareAction::class,
        //     ],

        //     /*
        //     * You can customize the way in which the package resolves the queuable to a job.
        //     *
        //     * For example, using the package laravel-actions (by Loris Leiva), you can
        //     * resolve JobDecorator to getAction() like so: JobDecorator::class => 'getAction'
        //     */
        //     'queueable_to_job' => [
        //         \Illuminate\Mail\SendQueuedMailable::class => 'mailable',
        //         \Illuminate\Notifications\SendQueuedNotifications::class => 'notification',
        //         \Illuminate\Events\CallQueuedListener::class => 'class',
        //         \Illuminate\Broadcasting\BroadcastEvent::class => 'event',
        //     ],
        // ],
    ],
];