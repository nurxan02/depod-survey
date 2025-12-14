<?php
/**
 * Question Model - Handles all database operations for questions
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';

class Question {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all questions ordered by order field
     */
    public function getAllQuestions() {
        $sql = "SELECT * FROM questions ORDER BY `order` ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Get question by ID
     */
    public function getQuestionById($id) {
        $sql = "SELECT * FROM questions WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->fetch();
    }
    
    /**
     * Get question with options
     */
    public function getQuestionWithOptions($questionId) {
        $question = $this->getQuestionById($questionId);
        if ($question) {
            $question['options'] = $this->getOptionsByQuestionId($questionId);
        }
        return $question;
    }
    
    /**
     * Get options by question ID
     */
    public function getOptionsByQuestionId($questionId) {
        $sql = "SELECT * FROM options WHERE question_id = :question_id ORDER BY id ASC";
        return $this->db->query($sql)->bind(':question_id', $questionId)->fetchAll();
    }
    
    /**
     * Get all questions with their options
     */
    public function getAllQuestionsWithOptions() {
        $questions = $this->getAllQuestions();
        foreach ($questions as &$question) {
            $question['options'] = $this->getOptionsByQuestionId($question['id']);
        }
        return $questions;
    }
    
    /**
     * Create new question
     */
    public function createQuestion($questionText, $order) {
        $sql = "INSERT INTO questions (question_text, `order`) VALUES (:question_text, :order)";
        $this->db->query($sql)
            ->bind(':question_text', $questionText)
            ->bind(':order', $order)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Update question
     */
    public function updateQuestion($id, $questionText, $order) {
        $sql = "UPDATE questions SET question_text = :question_text, `order` = :order WHERE id = :id";
        return $this->db->query($sql)
            ->bind(':id', $id)
            ->bind(':question_text', $questionText)
            ->bind(':order', $order)
            ->execute();
    }
    
    /**
     * Delete question
     */
    public function deleteQuestion($id) {
        $sql = "DELETE FROM questions WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->execute();
    }
    
    /**
     * Get total question count
     */
    public function getQuestionCount() {
        $sql = "SELECT COUNT(*) as count FROM questions";
        $result = $this->db->query($sql)->fetch();
        return $result['count'];
    }
}
