<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Models\Usuario;
use \App\Models\Estado;

class AuthTest extends TestCase
{
    use DatabaseTransactions;
    private $user;
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNTk4NjAsImV4cCI6MTUxMDU2OTQ2MCwibmJmIjoxNTA5MzU5ODYwLCJqdGkiOiJvV2pYQUdFRGp6TnZwbXMzIn0.u8iepWlI-dpYWCR1V6w1pvcjUmd5nLfDni2I1kjKQGc';
    private $validToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjEuMTA4OjgwMDAvYXBpL3Rkcml2ZXIvdjEvbG9naW4iLCJpYXQiOjE1MDkzNzMyNjQsImV4cCI6MTUxMDU4Mjg2NCwibmJmIjoxNTA5MzczMjY0LCJqdGkiOiJiV3dEcTFCZWtMMnZsQXg0In0.OaGpB4NDPyob5NjQSSCOJ5xnacyblDWhhbNMGck7ABY';


    public function setUp() {
        parent::setUp();
        $this->user = Usuario::find(1);
    }

    // VERSION

    /**
     * Version test.
     *
     * @return String json indicando la version actual
     */
    public function testVersion() {

        $this->json('GET', '/api/tdriver/v1/version')
            ->seeJsonEquals(['version' => '1.50.0'])
            ->dontSeeJson(['version' => '1.50.1'])
            ->assertResponseStatus(200);
    }

    // LOGIN

    /**
     * Login success test.
     *
     * @return String json retrieving token
     */
    public function testLoginSuccess() {

        $this->json('POST', '/api/tdriver/v1/login', ['email' => 'test@transporter.es', 'password' => 'testr'])
            ->seeJsonStructure(['token'])
            ->assertResponseStatus(200);
    }

    /**
     * Login password error test.
     *
     * @return String status code 401
     */
    public function testLoginPasswordError() {

        $this->json('POST', '/api/tdriver/v1/login', ['email' => 'test@transporter.es', 'password' => 'tr'])
            ->assertResponseStatus(401);
    }

    /**
     * Login with non existing user error test.
     *
     * @return String status code 401
     */
    public function testLoginNonExistingUser() {

        $this->json('POST', '/api/tdriver/v1/login', ['email' => 'tester@transporter.es', 'password' => 'tr'])
            ->assertResponseStatus(401);
    }

    /**
     * Login with no email error test.
     *
     * @return String status code 401
     */
    public function testLoginNoEmail() {

        $this->json('POST', '/api/tdriver/v1/login', ['password' => 'tr'])
            ->assertResponseStatus(401);
    }

    /**
     * Login with no password error test.
     *
     * @return String status code 401
     */
    public function testLoginNoPassword() {

        $this->json('POST', '/api/tdriver/v1/login', ['email' => 'tester@transporter.es'])
            ->assertResponseStatus(401);
    }

    /**
     * Login with no data error test.
     *
     * @return String status code 401
     */
    public function testLoginNoData() {

        $this->json('POST', '/api/tdriver/v1/login', [])
            ->assertResponseStatus(401);
    }

    // LOGOUT

    /**
     * Logout success and cannot logout with same token test.
     *
     * @return String status code 200
     */
    public function testLogoutSuccess() {

        $this->post('/api/tdriver/v1/logout', [], ['authorization' => 'Bearer' . $this->token])
            ->assertResponseStatus(200);

        // No permite logout de nuevo con le mismo token
        $this->post('/api/tdriver/v1/logout', [], ['authorization' => 'Bearer' . $this->token])
            ->assertResponseStatus(401);
    }

    /**
     * Logout error with invalid token test.
     *
     * @return String status code 400
     */
    public function testLogoutInvalidToken() {

        $this->post('/api/tdriver/v1/logout', [], ['authorization' => 'Bearer' . '123'])
            ->assertResponseStatus(400);
    }

    /**
     * Logout error with missing token test.
     *
     * @return String status code 400
     */
    public function testLogoutMissingToken() {

        $this->post('/api/tdriver/v1/logout', [])
            ->assertResponseStatus(400);
    }

    // RESET PASSWORD

    /**
     * Reset password success test.
     *
     * @return String status code 400
     */
//    public function testResetPasswordSuccess() {
//
//        $this->get('/api/tdriver/v1/users/1/home', ['authorization' => 'Bearer' . $this->validToken])
//            ->assertResponseStatus(200);
//    }

}
