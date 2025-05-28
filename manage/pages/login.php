<style>
    .formcontainer {
        min-height: calc(100vh - 40px);
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .centerform {}
</style>

<div class="bg-white p-8 rounded-lg shadow-lg w-96">
    <h2 class="text-2xl font-semibold text-center mb-6">OTP Verification</h2>

    <form id="otpForm">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <!-- OTP Input -->
        <div class="mb-4">
            <label class="block text-gray-700">Enter OTP</label>
            <div class="flex">
                <input type="text" name="otp" id="otp" maxlength="6" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 text-center text-lg tracking-widest" required>
                <button type="button" id="resendOtp" class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">Resend</button>
            </div>
        </div>



        <!-- CAPTCHA Input -->
        <div class="mb-4">
            <label class="block text-gray-700">Enter Captcha</label>
            <div class="flex">
                <input type="text" name="captcha" id="captcha" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 text-center text-lg tracking-widest" required>
                <button type="button" id="refreshCaptcha" class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">Refresh</button>
            </div>
        </div>
        <!-- CAPTCHA Image -->
        <div class="mb-4">
            <img src="captcha.php" id="captchaImg" class="w-full h-16 mb-2 border rounded-lg">
            <!--<button type="button" id="" class="text-blue-500 text-sm">Refresh Captcha</button>-->
        </div>
        <!-- Submit Button -->
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">Verify</button>
    </form>

    <!-- Response Message -->
    <p id="responseMessage" class="text-center text-sm text-red-500 mt-4"></p>
</div>

<script>
    document.getElementById("otpForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch("api/verify", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseElem = document.getElementById("responseMessage");

                if (data.status) {
                    // Success: Green background, white text
                    responseElem.style.backgroundColor = "green";
                    responseElem.style.color = "white";
                    responseElem.innerText = data.message;

                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000); // Adjust delay as needed
                } else {
                    // Error: Red background, white text
                    responseElem.style.backgroundColor = "red";
                    responseElem.style.color = "white";
                    responseElem.innerText = data.message;
                }
            })
            .catch(error => {
                const responseElem = document.getElementById("responseMessage");
                responseElem.style.backgroundColor = "red";
                responseElem.style.color = "white";
                responseElem.innerText = error.message;
            });
    });

    document.getElementById("resendOtp").addEventListener("click", function() {
        otpbtn = document.getElementById("resendOtp");
        otpbtn.disabled = true;
        fetch("resend_otp.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("responseMessage").innerText = data;
                setTimeout(() => {
                    otpbtn = document.getElementById("resendOtp");
                    otpbtn.disabled = false;
                }, 5000); // Adjust delay as needed
            });
    });

    document.getElementById("refreshCaptcha").addEventListener("click", function() {
        document.getElementById("captchaImg").src = "captcha.php?" + Math.random();
    });
</script>