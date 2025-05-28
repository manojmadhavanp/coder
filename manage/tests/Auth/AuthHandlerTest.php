<?php

namespace Manage\Tests\Auth;

use PHPUnit\Framework\TestCase;
use Manage\Auth\AuthHandler;
use System\Database\DBServer; // The actual class to be mocked
use PDO; // PHP's PDO class, to be mocked
use PDOStatement; // PHP's PDOStatement class, to be mocked
use PDOException; // PHP's PDOException class

class AuthHandlerTest extends TestCase {
    private $dbServerMock;
    private $pdoMock;
    private $selectStmtMock; // For SELECT queries
    private $insertStmtMock; // For INSERT queries (logging)

    private const DUMMY_IP = '127.0.0.1';
    private const DUMMY_USER_AGENT = 'TestBrowser';

    protected function setUp(): void {
        $this->dbServerMock = $this->createMock(DBServer::class);
        $this->pdoMock = $this->createMock(PDO::class);
        $this->selectStmtMock = $this->createMock(PDOStatement::class);
        $this->insertStmtMock = $this->createMock(PDOStatement::class);

        $this->dbServerMock->method('getPdo')->willReturn($this->pdoMock);

        // Configure PDO mock to return different statement mocks based on SQL query
        $this->pdoMock->method('prepare')
            ->willReturnCallback(function ($sql) {
                if (stripos($sql, 'INSERT INTO authlog') === 0) {
                    return $this->insertStmtMock;
                }
                // Assuming all other queries are SELECTs for profile or auth
                return $this->selectStmtMock;
            });
        
        // Common expectation for bindParam on both statement types
        $this->selectStmtMock->method('bindParam')->willReturn(true);
        $this->insertStmtMock->method('bindParam')->willReturn(true);
        $this->insertStmtMock->method('bindValue')->willReturn(true); // For nullable sysauth_id
    }

    public function testLoginSuccessfulWithEmail() {
        $identifier = 'test@example.com';
        $password = 'password123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sysauthId = 1;
        $uuid = 'some-uuid-email-123';

        $this->selectStmtMock
            ->expects($this->exactly(2)) // Profile select, Auth select
            ->method('execute')
            ->willReturn(true);

        $this->selectStmtMock
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                // First call for sysprofile
                ['id' => 99, 'sysauth_id' => $sysauthId, 'first_name' => 'Test', 'last_name' => 'EmailUser', 'userrole' => 'admin', 'email' => $identifier, 'mobile' => null],
                // Second call for sysauth
                ['uuid' => $uuid, 'password' => $hashedPassword]
            ));
        
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true);

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertEquals($uuid, $result['user_data']['uuid']);
        $this->assertEquals('Test', $result['user_data']['first_name']);
    }

    public function testLoginSuccessfulWithMobile() {
        $identifier = '1234567890';
        $password = 'password123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sysauthId = 2;
        $uuid = 'some-uuid-mobile-456';

        $this->selectStmtMock->expects($this->exactly(2))->method('execute')->willReturn(true);
        $this->selectStmtMock->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => 100, 'sysauth_id' => $sysauthId, 'first_name' => 'Test', 'last_name' => 'MobileUser', 'userrole' => 'user', 'email' => null, 'mobile' => $identifier],
                ['uuid' => $uuid, 'password' => $hashedPassword]
            ));
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true);

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertEquals($uuid, $result['user_data']['uuid']);
    }

    public function testLoginFailureProfileNotFound() { // User not found in sysprofile
        $identifier = 'unknown@example.com';
        $password = 'password123';

        $this->selectStmtMock->expects($this->once())->method('execute')->willReturn(true); // For sysprofile query
        $this->selectStmtMock->expects($this->once())->method('fetch')->willReturn(false); // Profile not found
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true); // Log attempt

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid credentials.', $result['message']);
    }

    public function testLoginFailureAuthRecordNotFound() { // Found in sysprofile, but not in sysauth (integrity issue)
        $identifier = 'test@example.com';
        $password = 'password123';
        $sysauthId = 1;

        $this->selectStmtMock->expects($this->exactly(2))->method('execute')->willReturn(true); // Profile & Auth
        $this->selectStmtMock->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => 99, 'sysauth_id' => $sysauthId, 'first_name' => 'Test', 'last_name' => 'User', 'userrole' => 'admin', 'email' => $identifier, 'mobile' => null], // Profile found
                false // Auth record not found
            ));
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true); // Log attempt

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertFalse($result['success']);
        $this->assertEquals('Authentication error. Please contact support.', $result['message']);
    }
    
    public function testLoginFailureIncorrectPassword() {
        $identifier = 'test@example.com';
        $password = 'wrongpassword';
        $correctPassword = 'password123';
        $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT);
        $sysauthId = 1;
        $uuid = 'some-uuid-123';

        $this->selectStmtMock->expects($this->exactly(2))->method('execute')->willReturn(true);
        $this->selectStmtMock->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => 99, 'sysauth_id' => $sysauthId, 'first_name' => 'Test', 'last_name' => 'User', 'userrole' => 'admin', 'email' => $identifier, 'mobile' => null],
                ['uuid' => $uuid, 'password' => $hashedPassword]
            ));
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true);

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid credentials.', $result['message']);
    }

    public function testLoginHandlesDatabaseExceptionOnSelectPrepare() {
        $identifier = 'test@example.com';
        $password = 'password123';

        $this->pdoMock->method('prepare')
                      ->will($this->throwException(new PDOException("Database connection error on prepare")));
        
        // Even if prepare fails, _logLoginAttempt is called, which tries to prepare again.
        // So, the insertStmtMock should still be configured to execute.
        // For simplicity in this test, we assume the log attempt's prepare WILL succeed,
        // or that the callback for prepare differentiates.
        // The current callback in setUp will throw for any prepare if we just throw here.
        // Let's refine the callback:
        $this->pdoMock->method('prepare')
            ->willReturnCallback(function ($sql) {
                if (stripos($sql, 'SELECT ') === 0) { // Fail only for SELECTs
                    throw new PDOException("Simulated prepare error for SELECT");
                }
                // Allow INSERT to proceed for logging
                return $this->insertStmtMock; 
            });

        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true);


        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        $this->assertFalse($result['success']);
        $this->assertEquals('Database error during login. Please try again later.', $result['message']);
    }

    public function testLoginHandlesDatabaseExceptionOnSelectExecute() {
        $identifier = 'test@example.com';
        $password = 'password123';
    
        $this->selectStmtMock->method('execute')
                       ->will($this->throwException(new PDOException("Error during SELECT query execution")));
        $this->insertStmtMock->expects($this->once())->method('execute')->willReturn(true);
    
        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);
    
        $this->assertFalse($result['success']);
        $this->assertEquals('Database error during login. Please try again later.', $result['message']);
    }

    public function testLoginHandlesDatabaseExceptionOnLogInsertAndStillReturnsOriginalResult() {
        $identifier = 'test@example.com';
        $password = 'password123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sysauthId = 1;
        $uuid = 'some-uuid-email-123';

        // Simulate successful profile and auth lookup
        $this->selectStmtMock->expects($this->exactly(2))->method('execute')->willReturn(true);
        $this->selectStmtMock->method('fetch')
            ->will($this->onConsecutiveCalls(
                ['id' => 99, 'sysauth_id' => $sysauthId, 'first_name' => 'Test', 'last_name' => 'EmailUser', 'userrole' => 'admin', 'email' => $identifier, 'mobile' => null],
                ['uuid' => $uuid, 'password' => $hashedPassword]
            ));
        
        // Simulate failure on logging INSERT
        $this->insertStmtMock->expects($this->once())
                             ->method('execute')
                             ->will($this->throwException(new PDOException("Error during INSERT query execution")));

        $authHandler = new AuthHandler($this->dbServerMock);
        $result = $authHandler->login($identifier, $password, self::DUMMY_IP, self::DUMMY_USER_AGENT);

        // The login should still be considered successful because _logLoginAttempt catches its own exceptions
        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertEquals($uuid, $result['user_data']['uuid']);
    }
}
?>
