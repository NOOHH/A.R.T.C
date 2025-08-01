# Quiz Answer Format Fix

## Problem
The quiz system was incorrectly handling answers for multiple-choice questions. When a user selected an answer in letter format (e.g., "A"), but the correct answer was stored in index format (e.g., "0"), the system was failing to recognize correct answers.

## Diagnosis

1. **How answers are stored:**
   - Correct answers are stored in the database as indexes: "0" (first option), "1" (second option), etc.
   - User answers were being submitted as letters: "A" (first option), "B" (second option), etc.

2. **What was happening:**
   - User selects option "A" (first option)
   - The system checks if "A" === "0" (direct string comparison)
   - The comparison fails because "A" is not equal to "0"
   - The answer is incorrectly marked as wrong, even though the user selected the correct option

## Solution

Added a conversion function in the quiz take page to transform letter answers to index answers before submission:

```javascript
// Convert letter answers (A, B, C) to index answers (0, 1, 2)
function convertLetterAnswersToIndex(answers) {
    const convertedAnswers = {};
    
    for (const questionId in answers) {
        const answer = answers[questionId];
        
        // If answer is a letter (A, B, C, etc.)
        if (typeof answer === 'string' && answer.match(/^[A-Z]$/)) {
            // Convert to index (A=0, B=1, C=2, etc.)
            const index = answer.charCodeAt(0) - 65; // ASCII 'A' is 65
            convertedAnswers[questionId] = index.toString();
        } else {
            convertedAnswers[questionId] = answer;
        }
    }
    
    return convertedAnswers;
}
```

This function is called before submitting the answers:
```javascript
body: JSON.stringify({ answers: convertLetterAnswersToIndex(answers) })
```

## Testing

The fix was tested with multiple scenarios:

1. **Unit Tests:**
   - Converting "A" to "0" (passed)
   - Converting "B" to "1" (passed)
   - Converting "C" to "2" (passed)
   - Handling non-letter answers (passed)

2. **Integration Test:**
   - Simulated quiz submission with letter answers
   - Verified that converted answers match the expected index format
   - Confirmed that score calculation works correctly with the fix

## Files Modified

- `resources/views/student/quiz/take.blade.php`

## Impact

- Users will now have their answers correctly evaluated when taking quizzes
- Previously completed quizzes that were incorrectly marked may need to be reviewed
