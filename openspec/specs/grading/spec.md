# grading Specification

## Purpose

Lets the teacher record the grades they decided on (for example, accepting a batch of submissions, or half points for late work) from the AI assistant, with every grade previewed and approved before students see it. The AI never grades on its own.

## Requirements

### Requirement: Grade submissions
`grade_submission` SHALL save grades for one or more students on one assignment. A grade SHALL be a number within the assignment's maximum for point grading, or the name of a scale item (for example "Complete") for scale grading. An optional markdown feedback comment per student SHALL be saved as the assignment's feedback comment. It MUST require `mod/assign:grade` in the assignment's context and is subject to the confirmation protocol of `write-confirmation`.

#### Scenario: The teacher sees whose grade it is
- **WHEN** `grade_submission` returns a preview
- **THEN** every entry carries the student's name, not only their user ID

#### Scenario: Accept several submissions
- **WHEN** a teacher confirms grade "Complete" for five students on a scale-graded assignment
- **THEN** the five grades are saved, appear in the gradebook, and the result lists each student with the saved grade

#### Scenario: Half points for late work
- **WHEN** a teacher confirms 5 out of 10 points for a late submission with a feedback comment
- **THEN** the grade and the comment are saved on that student's submission

#### Scenario: Grade outside the scale
- **WHEN** a grade exceeds the maximum or names a scale item that does not exist
- **THEN** nothing is saved and the error lists the valid values

### Requirement: Grading follows the assignment's settings
Saved grades SHALL follow the assignment's existing settings as a grade saved in the web UI would: marking workflow, grade release and student notification. The preview SHALL state whether students will be notified and whether the grade will be visible to them immediately.

#### Scenario: Marking workflow in use
- **WHEN** the assignment uses a marking workflow and the teacher grades a submission
- **THEN** the grade is saved in the workflow state the teacher chose (default: not released), and the preview says students will not see it until released
