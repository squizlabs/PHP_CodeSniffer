<?php

public static function foo() {
}

/**
 * Adds a new issue.
 *
 * Returns the new issue id.
 *
 * @param string  $title             Title of the new issue.
 * @param string  $description       The description of the issue.
 * @param string  $reporter          Asset id of the reporter.
 * @param integer $projectid         Id of the project that the issue belongs to.
 * @param array   $tags              Array of tags.
 * @param string  $status            The status of the issue.
 * @param string  $assignedTo        The asset id of the user that the issue is
 *                                   assigned to.
 * @param string  $reportedDate      If set then this date will be used instead of the
 *                                   current date and time.
 * @param integer $reportedMilestone Reported milestone.
 *
 * @return integer
 * @throws ChannelException If there is an error.
 *
 * @api            write
 * @api-permission public
 */
public static function addIssue(
    $title,
    $description,
    $reporter=NULL,
    $projectid=NULL,
    array $tags=array(),
    $status=NULL,
    $assignedTo=NULL,
    $reportedDate=NULL,
    $reportedMilestone=NULL
) {
    // Get current projectid if not specified.
    if ($projectid === NULL) {
        Channels::includeSystem('Project');
        $projectid = Project::getCurrentProjectId();
        Channels::modifyBasket('project', $projectid);
    }

    Channels::includeSystem('SquizRoadmap');
    Channels::includeSystem('Permission');
    if (Permission::hasPermission($projectid, 'ideas.contribute') === FALSE) {
        throw new ChannelException(_('You do not have permission to contribute idea'));
    }

    if ($assignedTo !== NULL) {
        if (Permission::hasPermission($projectid, 'ideas.edit.details') === FALSE) {
            throw new ChannelException(_('You do not have permission to assign user to idea'));
        }

        if (SquizRoadmap::isVisibleProject($projectid, $assignedTo) === FALSE) {
            throw new ChannelException(_('Assigned to user does not have access to issue project.'));
        }
    }

    // Get current user id if not specified.
    if ($reporter === NULL) {
        Channels::includeSystem('User');
        $reporter = User::getCurrentUserid();
        Channels::modifyBasket('reporter', $reporter);
    }

    if (SquizRoadmap::isVisibleProject($projectid, $reporter) === FALSE) {
        throw new ChannelException(_('Contributed by user does not have access to issue project.'));
    }

    // Make sure status is valid.
    Channels::includeSystem('SquizRoadmap');
    Channels::includeSystem('SquizRoadmapStatus');
    if ($status === NULL) {
        $statuses = SquizRoadmapStatus::getStatus($projectid);
        if (empty($statuses) === TRUE) {
            throw new ChannelException(_('No defined statuses in project'));
        }

        $status = $statuses[0]['status'];
        Channels::modifyBasket('status', $status);
    } else if (SquizRoadmapStatus::isValidStatus($projectid, $status) === FALSE) {
        throw new ChannelException(sprintf(_('Invalid status: %s'), $status));
    }

    $issueid = DAL::seqNextVal('sq_rdm_issue_seq');
    Channels::addToBasket('issueid', $issueid);

    if ($reportedDate === NULL) {
        include_once 'Libs/String/String.inc';
        $reportedDate = String::tsIso8601(time());
        Channels::modifyBasket('reportedDate', $reportedDate);
    }

    $title = trim($title);
    Channels::modifyBasket('title', $title);
    if (empty($title) === TRUE) {
        throw new ChannelException(_('Title cannot be empty'));
    }

    $description = SquizRoadmap::stripTags(trim($description));
    Channels::modifyBasket('description', $description);
    if (empty($description) === TRUE) {
        throw new ChannelException(_('Description cannot be empty'));
    }

    try {
        DAL::beginTransaction();

        $query = DAL::getDALQuery('SquizRoadmapIssue', 'addIssue');
        DAL::executeQuery($query);

        // Add tags for the new issue.
        SquizRoadmapIssue::addIssueTags($issueid, $tags);

        // Add reporter and assignee to watch list.
        SquizRoadmapIssue::addIssueWatch($issueid, $reporter);

        if ($assignedTo !== NULL) {
            SquizRoadmapIssue::addIssueWatch($issueid, $assignedTo);
        }

        SquizRoadmapIssue::clearIssueCache($issueid);

        DAL::commit();
    } catch (Exception $e) {
        DAL::rollBack();
        throw new ChannelException($e->getMessage());
    }//end try

    if ($something === NULL) {
        if ($bar !== NULL) {
        }
    }

    return $issueid;

}//end addIssue()

/**
 * Adds a new issue.
 *
 * Returns the new issue id.
 *
 * @param string  $title             Title of the new issue.
 * @param string  $description       The description of the issue.
 * @param string  $reporter          Asset id of the reporter.
 * @param integer $projectid         Id of the project that the issue belongs to.
 * @param array   $tags              Array of tags.
 * @param string  $status            The status of the issue.
 * @param string  $assignedTo        The asset id of the user that the issue is
 *                                   assigned to.
 * @param string  $reportedDate      If set then this date will be used instead of the
 *                                   current date and time.
 * @param integer $reportedMilestone Reported milestone.
 *
 * @return integer
 * @throws ChannelException If there is an error.
 *
 */
public static function addIssue(
    $title,
    $description,
    $reporter=NULL,
    $projectid=NULL,
    array $tags=array(),
    $status=NULL,
    $assignedTo=NULL,
    $reportedDate=NULL,
    $reportedMilestone=NULL
) {
    // Get current projectid if not specified.
    if ($projectid === NULL) {
        Channels::includeSystem('Project');
        $projectid = Project::getCurrentProjectId();
        Channels::modifyBasket('project', $projectid);
    }

}//end addIssue()
