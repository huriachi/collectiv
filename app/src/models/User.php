<?php namespace collectiv\models;

use collectiv\core\MySQL;

class UserModel {
    private $id;
    private $first_name;
    private $surname;
    private $email;
    private $username;

    public function setId($id): void {
        $this->id = $id;
    }

    public function setFirstName($first_name): void {
        $this->first_name = $first_name;
    }

    public function setSurname($surname): void {
        $this->surname = $surname;
    }

    public function setEmail($email): void {
        $this->email = $email;
    }

    public function setUsername($username): void {
        $this->username = $username;
    }

    public function getId() {
        return $this->id;
    }

    public function getFirstname() {
        return $this->first_name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function __set($name, $value) {
        // Make sure only defined properties will be set.
    }
}

class UserQuery {
    private $database;

    public function __construct(MySQL $database) {
        $this->database = $database;
    }

    /**
     * Retrieves all users from the database.
     *
     * @return UserModel[]
     */
    public function getAll() {
        return $this->database
            ->query('SELECT * FROM users')
            ->fetchAll(\PDO::FETCH_CLASS, UserModel::class);
    }

    /**
     * Retrieves a user with the provided ID.
     *
     * @param int $id
     * @return UserModel
     */
    public function get(int $id): UserModel {
        $statement = $this->database->prepare('SELECT * FROM users WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetchObject(UserModel::class);
    }

    /**
     * Persists the given user model to the database. Their password will also be saved.
     *
     * @param UserModel $user
     * @param string $password
     * @return UserModel
     */
    public function create(UserModel $user, string $password): UserModel {
        $statement = $this->database->prepare(
            'INSERT INTO users (first_name, surname, email, username, password) VALUES (?, ?, ?, ?, ?)'
        );

        $statement->execute([
            $user->getFirstname(),
            $user->getSurname(),
            $user->getEmail(),
            $user->getUsername(),
            $this->generatePassword($password)
        ]);

        $user->setId($this->database->lastInsertId());
        return $user;
    }

    /**
     * Updates the provided user model that resides in the database. Their password can optionally be updated.
     *
     * @param UserModel $user
     * @param string|null $password
     * @return UserModel
     */
    public function update(UserModel $user, string $password = null): UserModel {
        $queryString = 'UPDATE users SET first_name = ?, surname = ?, email = ?, username = ?';
        $fields = [$user->getFirstname(), $user->getSurname(), $user->getEmail(), $user->getUsername()];

        if (!is_null($password)) {
            $queryString .= ', password = ?';
            $fields[] = $this->generatePassword($password);
        }

        $queryString .= ' WHERE id = ?';
        $fields[] = $user->getId();

        $statement = $this->database->prepare($queryString);
        $statement->execute($fields);
        return $user;
    }

    /**
     * Removes the user that has the provided ID from the database.
     *
     * @param int $id
     */
    public function delete(int $id): void {
        $statement = $this->database->prepare('DELETE FROM users WHERE id = ?');
        $statement->execute([$id]);
    }

    /**
     * Checks to see if a given value does not already exist in the database. The original value is meant for updates
     * where a user may choose to keep existing information.
     *
     * @param string $field
     * @param $value
     * @param string $original
     * @return bool
     */
    public function isFieldUnique(string $field, $value, $original = '') {
        $statement = $this->database->prepare("SELECT $field FROM users WHERE $field = ?");
        $statement->execute([$value]);
        $found = $statement->fetchColumn();

        if ($statement->rowCount() > 0 && $found !== $original) {
            return false;
        }
        return true;
    }

    /**
     * Generates a password hash using the PHP standard algorithm.
     *
     * @param string $password
     * @return string
     */
    private function generatePassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}