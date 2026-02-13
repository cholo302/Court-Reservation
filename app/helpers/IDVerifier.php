<?php
/**
 * Philippine Government ID Verification Helper
 * Validates and verifies Philippine Government IDs
 */

class IDVerifier {
    
    // Valid ID types
    const VALID_ID_TYPES = [
        'lto_license' => 'LTO Driver\'s License',
        'passport' => 'Philippine Passport',
        'nbi' => 'NBI Clearance',
        'barangay_id' => 'Barangay ID',
        'national_id' => 'National ID',
        'sss_id' => 'SSS ID',
        'tin_id' => 'TIN ID',
        'prc_id' => 'PRC License',
        'postal_id' => 'Postal ID',
    ];
    
    /**
     * Check if ID type is valid
     */
    public static function isValidIDType($idType) {
        return isset(self::VALID_ID_TYPES[$idType]);
    }
    
    /**
     * Get human-readable name for ID type
     */
    public static function getIDTypeName($idType) {
        return self::VALID_ID_TYPES[$idType] ?? null;
    }
    
    /**
     * Get all valid ID types
     */
    public static function getAllIDTypes() {
        return self::VALID_ID_TYPES;
    }
    
    /**
     * Validate ID Number based on ID Type
     * Returns: ['valid' => bool, 'message' => string]
     */
    public static function validateIDNumber($idType, $idNumber) {
        $idNumber = preg_replace('/\s+/', '', $idNumber); // Remove spaces
        
        switch ($idType) {
            case 'lto_license':
                return self::validateLTOLicense($idNumber);
            case 'passport':
                return self::validatePassport($idNumber);
            case 'nbi':
                return self::validateNBI($idNumber);
            case 'barangay_id':
                return self::validateBarangayID($idNumber);
            case 'national_id':
                return self::validateNationalID($idNumber);
            case 'sss_id':
                return self::validateSSSID($idNumber);
            case 'tin_id':
                return self::validateTINID($idNumber);
            case 'prc_id':
                return self::validatePRCID($idNumber);
            case 'postal_id':
                return self::validatePostalID($idNumber);
            default:
                return ['valid' => false, 'message' => 'Invalid ID type'];
        }
    }
    
    /**
     * Validate LTO Driver's License
     * Format: 00-00-000000-0 or similar numeric format
     */
    private static function validateLTOLicense($idNumber) {
        // LTO License has 10-13 digits
        if (!preg_match('/^\d{10,13}$/', str_replace('-', '', $idNumber))) {
            return [
                'valid' => false,
                'message' => 'Invalid LTO License format. Expected 10-13 digits (e.g., 0012345678)'
            ];
        }
        
        if (strlen(str_replace('-', '', $idNumber)) < 10) {
            return [
                'valid' => false,
                'message' => 'LTO License number is too short'
            ];
        }
        
        return ['valid' => true, 'message' => 'Valid LTO License'];
    }
    
    /**
     * Validate Philippine Passport
     * Format: 2 letters + 6-7 digits (e.g., PA1234567)
     */
    private static function validatePassport($idNumber) {
        if (!preg_match('/^[A-Z]{2}\d{6,7}$/', $idNumber)) {
            return [
                'valid' => false,
                'message' => 'Invalid Passport format. Expected 2 letters + 6-7 digits (e.g., PA1234567)'
            ];
        }
        return ['valid' => true, 'message' => 'Valid Philippine Passport'];
    }
    
    /**
     * Validate NBI Clearance
     * Format: numeric, typically 9-13 digits
     */
    private static function validateNBI($idNumber) {
        if (!preg_match('/^\d{9,13}$/', $idNumber)) {
            return [
                'valid' => false,
                'message' => 'Invalid NBI Clearance format. Expected 9-13 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid NBI Clearance'];
    }
    
    /**
     * Validate Barangay ID
     * Format: flexible, typically numeric or alphanumeric
     */
    private static function validateBarangayID($idNumber) {
        if (strlen($idNumber) < 5 || strlen($idNumber) > 20) {
            return [
                'valid' => false,
                'message' => 'Invalid Barangay ID format. Length should be 5-20 characters'
            ];
        }
        return ['valid' => true, 'message' => 'Valid Barangay ID'];
    }
    
    /**
     * Validate National ID
     * Format: 12 digits (YYYYMMDD-XXXX)
     */
    private static function validateNationalID($idNumber) {
        $clean = str_replace('-', '', $idNumber);
        if (!preg_match('/^\d{12}$/', $clean)) {
            return [
                'valid' => false,
                'message' => 'Invalid National ID format. Expected 12 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid National ID'];
    }
    
    /**
     * Validate SSS ID
     * Format: 10 digits (SSSS-SSSS-SS)
     */
    private static function validateSSSID($idNumber) {
        $clean = str_replace('-', '', $idNumber);
        if (!preg_match('/^\d{10}$/', $clean)) {
            return [
                'valid' => false,
                'message' => 'Invalid SSS ID format. Expected 10 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid SSS ID'];
    }
    
    /**
     * Validate TIN ID
     * Format: 12 digits (XXX-XXX-XXX-XXX)
     */
    private static function validateTINID($idNumber) {
        $clean = str_replace('-', '', $idNumber);
        if (!preg_match('/^\d{12}$/', $clean)) {
            return [
                'valid' => false,
                'message' => 'Invalid TIN ID format. Expected 12 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid TIN ID'];
    }
    
    /**
     * Validate PRC License
     * Format: 7 digits
     */
    private static function validatePRCID($idNumber) {
        if (!preg_match('/^\d{7}$/', $idNumber)) {
            return [
                'valid' => false,
                'message' => 'Invalid PRC License format. Expected 7 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid PRC License'];
    }
    
    /**
     * Validate Postal ID
     * Format: 13 digits
     */
    private static function validatePostalID($idNumber) {
        $clean = str_replace('-', '', $idNumber);
        if (!preg_match('/^\d{13}$/', $clean)) {
            return [
                'valid' => false,
                'message' => 'Invalid Postal ID format. Expected 13 digits'
            ];
        }
        return ['valid' => true, 'message' => 'Valid Postal ID'];
    }
}
