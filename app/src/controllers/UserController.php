<?php namespace collectiv\controllers;

use collectiv\core\View;
use collectiv\models\UserModel;
use collectiv\models\UserQuery;
use Klein\Exceptions\ValidationException;
use Klein\Request;
use Klein\Response;
use Klein\ServiceProvider;

class UserController extends BaseController {

    /**
     * This route displays all users that are on the system.
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index() {
        $userQuery = new UserQuery($this->database);
        $users = $userQuery->getAll();
        return View::render('userlist.twig', ['users' => $users]);
    }

    /**
     * This route displays the form for user creation.
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function create() {
        return View::render('user.twig');
    }

    /**
     * This route handles user creation.
     *
     * @param Request $request
     * @param Response $response
     * @param ServiceProvider $service
     */
    public function store(Request $request, Response $response, ServiceProvider $service) {
        $errors = $this->getFormErrors($request, $service);

        if (count($errors) === 0) {
            $response->code(200);

            $userQuery = new UserQuery($this->database);
            $userModel = new UserModel();
            $userModel->setFirstName($request->firstname);
            $userModel->setSurname($request->lastname);
            $userModel->setEmail($request->email);
            $userModel->setEmail($request->email);
            $userModel->setUsername($request->username);
            $userQuery->create($userModel, $request->password);

            $response->json([
                'success' => true,
                'message' => 'The user has been created.'
            ]);
        } else {
            $response->code(400);
            $response->json([
                'success' => false,
                'message' => 'Please check the indicated fields for issues.',
                'errors' => $errors
            ]);
        }
    }

    /**
     * This route shows the form for user editing.
     *
     * @param Request $request
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function edit(Request $request) {
        $userQuery = new UserQuery($this->database);
        $user = $userQuery->get($request->id);
        return View::render('user.twig', ['user' => $user]);
    }

    /**
     * This route handles user updating.
     *
     * @param Request $request
     * @param Response $response
     * @param ServiceProvider $service
     */
    public function update($request, $response, $service) {
        $userQuery = new UserQuery($this->database);
        $userModel = $userQuery->get($request->id);
        $errors = $this->getFormErrors($request, $service, $userModel);

        if (count($errors) === 0) {
            $response->code(200);

            $userModel->setFirstName($request->firstname);
            $userModel->setSurname($request->lastname);
            $userModel->setEmail($request->email);
            $userModel->setUsername($request->username);

            if ($request->password === '') {
                $userQuery->update($userModel);
            } else {
                $userQuery->update($userModel, $request->password);
            }

            $response->json([
                'success' => true,
                'message' => 'The user has been edited.'
            ]);
        } else {
            $response->code(400);
            $response->json([
                'success' => false,
                'message' => 'Please check the indicated fields for issues.',
                'errors' => $errors
            ]);
        }
    }

    /**
     * This route allows for the deletion of a user.
     *
     * @param Request $request
     */
    public function delete(Request $request) {
        $userQuery = new UserQuery($this->database);
        $userQuery->delete($request->id);
    }

    /**
     * Validates that the given string is within the supplied range.
     *
     * @param string $value
     * @param int $min
     * @param int $max
     */
    private function validateString(string $value, int $min = 1, int $max = 255) {
        if (strlen($value) < $min) {
            throw new ValidationException("This is a bit too short. Please use at least $min characters.");
        }

        if (strlen($value) > $max) {
            throw new ValidationException("This is a bit too long. Please use at most $max characters.");
        }
    }

    /**
     * Validates that the provided user entry is unique in the database.
     *
     * @param string $parameter
     * @param string $value
     * @param string $original
     */
    private function validateUnique(string $parameter, string $value, string $original = '') {
        $userQuery = new UserQuery($this->database);
        if (!$userQuery->isFieldUnique($parameter, $value, $original)) {
            throw new ValidationException('This is already in use.');
        }
    }

    /**
     * Validates a given password and checks that the verification password matches.
     *
     * @param string $password
     * @param string $verify
     */
    private function validatePassword(string $password, string $verify) {
        $this->validateString($password, 5);
        if ($password !== $verify) {
            throw new ValidationException('Your passwords do not match.');
        }
    }

    /**
     * Validates the form data that has been entered by the user. All errors will be returned which can be used on
     * the front-end for user guidance.
     *
     * @param Request $request
     * @param ServiceProvider $service
     * @param UserModel|null $userModel
     * @return array
     */
    private function getFormErrors(Request $request, ServiceProvider $service, UserModel $userModel = null) {
        $errors = [];

        try {
            $this->validateString($request->firstname);
        } catch (ValidationException $exception) {
            $errors['firstname'] = $exception->getMessage();
        }

        try {
            $this->validateString($request->lastname);
        } catch (ValidationException $exception) {
            $errors['lastname'] = $exception->getMessage();
        }

        try {
            $service->validateParam('email', 'This is not a valid email.')->isEmail();
            if (!is_null($userModel)) {
                $this->validateUnique('email', $request->email, $userModel->getEmail());
            } else {
                $this->validateUnique('email', $request->email);
            }
        } catch (ValidationException $exception) {
            $errors['email'] = $exception->getMessage();
        }

        try {
            $this->validateString($request->username);
            if (!is_null($userModel)) {
                $this->validateUnique('username', $request->username, $userModel->getUsername());
            } else {
                $this->validateUnique('username', $request->username);
            }
        } catch (ValidationException $exception) {
            $errors['username'] = $exception->getMessage();
        }

        if (is_null($userModel) || $request->password !== '') {
            try {
                $this->validatePassword($request->password, $request->password_verify);
            } catch (ValidationException $exception) {
                $errors['password'] = $exception->getMessage();
                $errors['password_verify'] = $exception->getMessage();
            }
        }
        
        return $errors;
    }

    protected function routeName(): string {
        return 'users';
    }
}