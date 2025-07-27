import { GoogleGenerativeAI } from "https://esm.run/@google/generative-ai";

const API_KEY = "AIzaSyApwLadkEmUpUe8kv5Nl5-7p35ob9_DSsY";
const genAI = new GoogleGenerativeAI(API_KEY);
const model = genAI.getGenerativeModel({
    model: "gemini-2.0-flash",
    systemInstruction: `You are an expert educational content creator specialized in generating high-quality quiz questions.

Your task is to create quiz questions based on the provided content with the specified parameters.

IMPORTANT FORMATTING REQUIREMENTS:
- Return ONLY a valid JSON array
- Each question must have this exact structure:
{
  "question": "Question text here",
  "type": "multiple_choice",
  "options": {
    "a": "Option A text",
    "b": "Option B text", 
    "c": "Option C text",
    "d": "Option D text"
  },
  "correct_answer": "a",
  "explanation": "Brief explanation of why this answer is correct"
}

QUALITY GUIDELINES:
- Questions should test understanding, not just memorization
- Each question should be clear and unambiguous
- All options should be plausible but only one correct
- Avoid trick questions or overly complex wording
- Ensure questions cover different aspects of the content
- Explanations should be educational and helpful

Do not include any text before or after the JSON array. Return only the JSON.`,
});

/**
 * Generate quiz questions from text content
 * @param {string} content - The text content to generate questions from
 * @param {Object} options - Generation options
 * @returns {Promise<Array>} Array of quiz questions
 */
async function generateQuizFromText(content, options = {}) {
    const {
        numQuestions = 10,
        difficulty = 'Medium',
        quizType = 'multiple_choice',
        topic = 'General'
    } = options;

    try {
        const prompt = `Based on the following content about ${topic}, generate exactly ${numQuestions} ${quizType} questions at ${difficulty} difficulty level:

CONTENT:
${content}

Generate the questions now as a JSON array:`;

        const result = await model.generateContent(prompt);
        const response = await result.response;
        const text = response.text();

        // Clean the response text
        let cleanText = text.replace(/```json\s*/g, '').replace(/```\s*$/g, '').trim();
        
        try {
            const questions = JSON.parse(cleanText);
            return questions.map(q => ({
                question: q.question,
                type: q.type || 'multiple_choice',
                options: q.options,
                correct_answer: q.correct_answer,
                explanation: q.explanation || '',
                points: 1
            }));
        } catch (parseError) {
            console.error('Error parsing Gemini response:', parseError);
            console.log('Raw response:', text);
            throw new Error('Failed to parse quiz questions from AI response');
        }

    } catch (error) {
        console.error('Error generating quiz from text:', error);
        throw error;
    }
}

/**
 * Generate quiz questions from uploaded file
 * @param {File} file - The uploaded file
 * @param {Object} options - Generation options
 * @returns {Promise<Array>} Array of quiz questions
 */
async function generateQuizFromFile(file, options = {}) {
    try {
        const content = await extractTextFromFile(file);
        if (!content) {
            throw new Error('No content could be extracted from the file');
        }
        
        return await generateQuizFromText(content, options);
    } catch (error) {
        console.error('Error generating quiz from file:', error);
        throw error;
    }
}

/**
 * Extract text content from file
 * @param {File} file - The file to extract text from
 * @returns {Promise<string>} Extracted text content
 */
async function extractTextFromFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                const content = e.target.result;
                
                // Basic text extraction based on file type
                if (file.type === 'text/plain') {
                    resolve(content);
                } else if (file.type === 'application/pdf') {
                    // For PDF files, you might want to use a proper PDF parser
                    // This is a very basic approach
                    resolve(content);
                } else {
                    // For other file types, try to extract readable text
                    const cleanContent = content.replace(/[^\x20-\x7E\n\r\t]/g, ' ');
                    resolve(cleanContent);
                }
            } catch (error) {
                reject(error);
            }
        };
        
        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsText(file);
    });
}

/**
 * Test connection to Gemini API
 * @returns {Promise<boolean>} Connection status
 */
async function testConnection() {
    try {
        const result = await model.generateContent('Test connection');
        const response = await result.response;
        return response && response.text();
    } catch (error) {
        console.error('Gemini API connection test failed:', error);
        return false;
    }
}

export { 
    model, 
    generateQuizFromText, 
    generateQuizFromFile, 
    extractTextFromFile, 
    testConnection 
};
