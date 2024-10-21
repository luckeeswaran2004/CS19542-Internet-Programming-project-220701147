<?php
// Database connection
$servername = "localhost"; // Update if needed
$username = "root"; // Update with your DB username
$password = ""; // Update with your DB password
$dbname = "dbip"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (!empty($email) && !empty($password)) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists
            echo "<script>
                    document.getElementById('error-message').innerHTML = 'An account with this email already exists.';
                    document.getElementById('error-message').classList.add('error');
                  </script>";
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO login (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hashedPassword);

            if ($stmt->execute()) {
                // Registration successful
                echo "<script>
                        document.getElementById('success-message').innerHTML = 'Registration successful! Please log in.';
                        document.getElementById('success-message').classList.add('success');
                      </script>";
            } else {
                // Registration failed
                echo "<script>
                        document.getElementById('error-message').innerHTML = 'Registration failed. Please try again.';
                        document.getElementById('error-message').classList.add('error');
                      </script>";
            }
            $stmt->close();
        }
    } else {
        // Missing fields
        echo "<script>
                document.getElementById('error-message').innerHTML = 'Please fill in both email and password fields.';
                document.getElementById('error-message').classList.add('error');
              </script>";
    }
}

$conn->close();
?>
