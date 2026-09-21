## Purpose

Lets the AI turn course material into questions and quizzes, the largest gain over core web services, which cannot create questions or quizzes at all. Targets the Moodle 5.x question bank model.

## ADDED Requirements

### Requirement: Import questions
`import_questions` SHALL import questions given in GIFT or Moodle XML into a named category of either the course's shared question bank or a quiz's own bank, using Moodle's own importers, so that the same validation applies as in the web UI import. The category SHALL be created if it does not exist. The result SHALL list the IDs and names of the imported questions.

#### Scenario: GIFT into the shared bank
- **WHEN** a teacher imports 15 multiple-choice questions in GIFT into category "Week 4" of the course's shared bank
- **THEN** 15 questions exist in that category and the result lists their IDs

#### Scenario: No shared bank yet
- **WHEN** the course has no shared question bank
- **THEN** one is created in the course before importing

#### Scenario: Parse error
- **WHEN** the input contains a question the importer cannot parse
- **THEN** no question is imported and the result reports the error with the position of the faulty question

#### Scenario: Missing permission
- **WHEN** the user lacks `moodle/question:add` in the target bank
- **THEN** nothing is imported and the error names the capability

### Requirement: Create quiz
`create_quiz` SHALL create a Quiz activity in a course section with a name, markdown introduction, optional open and close times, time limit, allowed attempts, question order shuffling and a review-options preset (`practice`: answers and feedback shown after each attempt, or `exam`: shown after the quiz closes). It SHALL return the module ID and URL. It MUST require `moodle/course:manageactivities` and `mod/quiz:addinstance`.

#### Scenario: Practice quiz until Friday
- **WHEN** a teacher creates a quiz with close time Friday 23:59, unlimited attempts and preset `practice`
- **THEN** the quiz exists with those settings and shows answers after each attempt

### Requirement: Add questions to a quiz
`add_questions_to_quiz` SHALL add specific questions by ID, or a number of random questions drawn from a category, to a quiz, with a mark per question and a number of questions per page, and SHALL set the quiz's maximum grade to what the questions are worth together unless `max_grade` gives another value. It MUST require `mod/quiz:manage` in the quiz and `moodle/question:useall` in the source bank.

#### Scenario: Maximum grade follows the questions
- **WHEN** questions worth 8 marks together are added to a quiz
- **THEN** the quiz's maximum grade becomes 8, so Moodle does not scale the marks, unless the teacher gives a maximum of their own

#### Scenario: Random questions from a category
- **WHEN** a teacher adds 10 random questions from category "Week 4" that holds 15 questions
- **THEN** the quiz has 10 random slots drawing from that category and its maximum grade reflects the marks

#### Scenario: Not enough questions
- **WHEN** a teacher asks for more random questions than the category holds
- **THEN** nothing is added and the error states how many questions the category holds

#### Scenario: Quiz already attempted
- **WHEN** the quiz already has student attempts
- **THEN** nothing is added and the error explains that the quiz structure is locked, as in the web UI

### Requirement: Supported Moodle versions
The quiz tools SHALL work on Moodle 5.0 and later. On earlier versions they MUST fail with a clear error instead of acting on the pre-5.0 question bank.

#### Scenario: Moodle 4.5 site
- **WHEN** `import_questions` is called on a Moodle 4.5 site
- **THEN** it fails with an error saying Moodle 5.0 or later is required
