<?php

namespace Manage\Auth;

use PDO;
use PDOException;
use System\Database\DBServer; // Assuming DBServer is autoloaded or included

class AuthHandler {
    private PDO $db;

    public function __construct(DBServer $dbServer) {
        // In the previous version, this was $dbServer->getPdo().
        // Assuming DBServer class itself is the PDO wrapper or provides getPdo().
        // Based on previous subtasks, DBServer has getPdo().
        $this->db = $dbServer->getPdo();
    }

    public function login(string $identifier, string $password, ?string $ipAddress, ?string $userAgent): array {
        $profile = null;
        $sysauth_id_for_log = null;
        // Determine identifier type (e.g., 'email' or 'mobile') based on $identifier
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        try {
            // Step 1: Find user in sysprofile
            $sqlProfile = "SELECT id, sysauth_id, first_name, last_name, userrole, email, mobile FROM sysprofile WHERE ";
            if ($isEmail) {
                $sqlProfile .= "email = :identifier";
            } else {
                // Assuming if not email, it's mobile. Add more validation if needed.
                $sqlProfile .= "mobile = :identifier";
            }
            
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bindParam(':identifier', $identifier);
            $stmtProfile->execute();
            $profile = $stmtProfile->fetch(PDO::FETCH_ASSOC);

            if (!$profile) {
                $this->_logLoginAttempt(null, $identifier, $ipAddress, $userAgent, false);
                return ['success' => false, 'message' => 'Invalid credentials.'];
            }
            $sysauth_id_for_log = (int)$profile['sysauth_id'];

            // Step 2: Fetch auth details from sysauth
            $stmtAuth = $this->db->prepare("SELECT uuid, password FROM sysauth WHERE id = :sysauth_id");
            $stmtAuth->bindParam(':sysauth_id', $profile['sysauth_id'], PDO::PARAM_INT);
            $stmtAuth->execute();
            $authData = $stmtAuth->fetch(PDO::FETCH_ASSOC);

            if (!$authData) {
                // This indicates a data integrity problem
                error_log("CRITICAL: Auth record not found for sysauth_id: " . $profile['sysauth_id']);
                $this->_logLoginAttempt($sysauth_id_for_log, $identifier, $ipAddress, $userAgent, false);
                return ['success' => false, 'message' => 'Authentication error. Please contact support.'];
            }

            // Step 3: Verify password
            if (password_verify($password, $authData['password'])) {
                $this->_logLoginAttempt($sysauth_id_for_log, $identifier, $ipAddress, $userAgent, true);
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user_data' => [
                        'uuid' => $authData['uuid'],
                        'first_name' => $profile['first_name'],
                        'last_name' => $profile['last_name'],
                        'userrole' => $profile['userrole'],
                        'email' => $profile['email'], // From sysprofile
                        'mobile' => $profile['mobile'] // From sysprofile
                    ]
                ];
            } else {
                $this->_logLoginAttempt($sysauth_id_for_log, $identifier, $ipAddress, $userAgent, false);
                return ['success' => false, 'message' => 'Invalid credentials.'];
            }

        } catch (PDOException $e) {
            error_log("AuthHandler PDOException: " . $e->getMessage());
            // Attempt to log failure even if main DB operations failed
            // $sysauth_id_for_log might be null if the first query failed, which is fine.
            $this->_logLoginAttempt($sysauth_id_for_log, $identifier, $ipAddress, $userAgent, false);
            // Return a generic error message to the client
            return ['success' => false, 'message' => 'Database error during login. Please try again later.'];
        }
    }

    private function _logLoginAttempt(?int $sysauth_id, string $identifierAttempted, ?string $ipAddress, ?string $userAgent, bool $success): void {
        try {
            $sqlLog = "INSERT INTO authlog (sysauth_id, identifier_attempted, ip_address, user_agent, login_success, login_time) 
                       VALUES (:sysauth_id, :identifier_attempted, :ipAddress, :userAgent, :success, NOW())";
            $stmtLog = $this->db->prepare($sqlLog);
            
            // Bind sysauth_id, allowing for null
            if ($sysauth_id === null) {
                $stmtLog->bindValue(':sysauth_id', null, PDO::PARAM_NULL);
            } else {
                $stmtLog->bindParam(':sysauth_id', $sysauth_id, PDO::PARAM_INT);
            }
            
            $stmtLog->bindParam(':identifier_attempted', $identifierAttempted);
            $stmtLog->bindParam(':ipAddress', $ipAddress); // PDO handles nulls by default if not specified otherwise
            $stmtLog->bindParam(':userAgent', $userAgent);
            $stmtLog->bindParam(':success', $success, PDO::PARAM_BOOL);
            $stmtLog->execute();
        } catch (PDOException $e) {
            // Log this error specifically, but don't let it break the login flow
            error_log("Failed to log login attempt for identifier '{$identifierAttempted}': " . $e->getMessage());
        }
    }
}
?>
