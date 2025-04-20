<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account Modal</title>

    <style>
        /* Modal Background */
        #createmodal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        /* Modal Content */
        .modal-create-content {
            background-color: #fff;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            padding: 20px 30px;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-out;
        }

        /* Close Button */
        .close-create {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* Header */
        .modal-create-content h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Form Labels */
        .modal-create-content label {
            display: block;
            margin: 8px 0 5px;
            font-weight: 500;
            font-size: 14px;
        }

        /* Form Inputs */
        .modal-create-content input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }

        .modal-create-content input:focus {
            border-color: #007bff;
        }

        /* Inline Form Group */
        .inline-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .inline-group div {
            flex: 1;
        }

        /* Error Messages */
        .error-message-create {
            color: red;
            font-size: 12px;
            margin-top: 3px;
            display: block;
            height: 14px;
        }

        /* Password Field with Toggle */
        .password-container {
            position: relative;
        }

        .password-container input {
            width: 100%;
            padding-right: 40px; /* Space for icon */
        }

        .password-container img {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
            height: 20px;
        }

        /* Terms Checkbox */
        .terms-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 13px;
        }

        .terms-container input {
            margin-right: 8px;
        }

        .terms-container a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .terms-container a:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .proceed-create-btn {
            width: 100%;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        .proceed-create-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .proceed-create-btn:hover:not(:disabled) {
            background-color: #218838;
        }

        /* Animation for modal */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 500px) {
            .modal-create-content {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Button to Open Modal -->
    <button onclick="openCreateModal()">Open Create Account Modal</button>

    <!-- Create Account Modal -->
    <div id="createmodal" class="modal-create">
        <div class="modal-create-content">
          
            <!-- Close Button -->
            <button class="close-create" onclick="closeCreateModal()">&times;</button>
            
            <h3>Create an Account</h3>
            
            <label for="createfullname">Full Name:</label>
            <input type="text" id="createfullname" placeholder="Enter your Full Name" required>
            <span class="error-message-create" id="createfullname-error"></span>
            
            <!-- Email and Phone -->
            <div class="inline-group">
                <div>
                    <label for="createemail">Email:</label>
                    <input type="text" id="createemail" placeholder="Enter your email" required>
                    <span class="error-message-create" id="createemail-error"></span>
                </div>
                <div>
                    <label for="createPhoneNumber">Phone Number:</label>
                    <input type="tel" id="createPhoneNumber" placeholder="Enter your Phone Number" required>
                    <span class="error-message-create" id="createPhoneNumber-error"></span>
                </div>
            </div>

            <!-- Password Fields -->
            <label for="createpassword">Password:</label>
            <div class="password-container">
                <input type="password" id="createpassword" placeholder="Enter your password" required>
                <img src="../Assests/hide.png" onclick="togglePassword('createpassword', this)">
            </div>

            <label for="confirmpassword">Confirm Password:</label>
            <div class="password-container">
                <input type="password" id="confirmpassword" placeholder="Confirm your password" required>
                <img src="../Assests/hide.png" onclick="togglePassword('confirmpassword', this)">
            </div>

            <!-- Terms Checkbox -->
            <div class="terms-container">
                <input type="checkbox" id="termsCheckbox" onchange="toggleProceedButton()">
                <label for="termsCheckbox">
                    I Agree to the terms and conditions <a href="#">read here</a>
                </label>
            </div>

            <!-- Create Account Button -->
            <button class="proceed-create-btn" id="proceedCreateBtn" onclick="createprocess()" disabled>Create your Account</button>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById("createmodal").style.display = "flex";
        }

        function closeCreateModal() {
            document.getElementById("createmodal").style.display = "none";
        }

        function toggleProceedButton() {
            document.getElementById("proceedCreateBtn").disabled = !document.getElementById("termsCheckbox").checked;
        }

        function togglePassword(id, element) {
            let input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>
