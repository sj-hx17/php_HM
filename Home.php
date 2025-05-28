<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HoloFo MaEM</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <style>
            /* Basic styles for modal */
            .fixed {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 50;
                font-family:Arial, Helvetica, sans-serif
            }

            body {
                background-image: url(Homepage.png);
                background-size: cover; 
                background-position: center; 
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                position: relative;
                font-family:monospace;
                font-weight: bold;
                overflow: scroll; 
            }

            .bg-black {
                background-color: rgba(0, 0, 0, 0.5);
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
            }

            .modal-content {
                background-color: white;
                border-radius: 0.5rem;
                padding: 2rem;
                max-width: 400px;
                width: 100%;
                position: relative;
                z-index: 10;
            }

            .window {
                border: 2px solid #0099cc; 
                border-radius: 8px;
                padding: 1%; 
                position: absolute;
                background-color: rgba(224, 247, 250, 0.9); 
                box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
                width: 80%; 
                max-width: 400px; 
            }

            .window-title {
                background: #007acc; 
                color: white;
                padding: 2%; 
                border-radius: 5px 5px 0 0;
                font-weight: bold;
                font-size: 1.2em; 
            }

            .window3 button {
                width: 100px;
                margin: 2px;
                height: 40px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
            }

            p {
                text-align: center;
                padding-left: 5%;
                font-size: 1.25em;
            }

            .image-container {
                display: flex; 
                justify-content: center; 
            }

            .responsive-image {
                width: 100%; 
                height: auto; 
                padding-top: 1.5%;
            }

            .login-container {
                align-items: center;
                text-align: center;        
            }
                    
            button {
                transition: all 0.3s ease;
                background-color: #007acc;
            }

            button:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
            }

            .window1 {
                height: auto; 
                width: 20%;
                top: calc(10vh); 
                left: calc(15vw); 
            }

            .window2 {
                height: auto;
                width: 30%; 
                top: calc(15vh); 
                left: calc(50vw); 
                transform: translateX(-50%); 
            }

            .window3 {
                height: auto;
                width: 25%; 
                top: calc(40vh); 
                right: calc(10vw); 
            }

            .progress-bar-container {
                width: 100%;
                background-color: #e0e0e0;
                border-radius: 0.25rem;
                height: 10px;
                overflow: hidden;
            }

            .progress-bar {
                background-color: #3b82f6;
                height: 100%;
                width: 0%;
                transition: width 0.3s ease;
            }

            @media (max-width: 990px) {
                body {
                    flex-direction: column; 
                    align-items: center; 
                    height: auto; 
                }

                .window {
                    position: relative; 
                    margin: 10px 0; 
                    width: 90%; 
                    max-width: 350px; 
                }

                .window1, .window2, .window3 {
                    top: auto; 
                    left: auto; 
                    right: auto; 
                    transform: none; 
                }
                
                p {
                    text-align: center;
                    padding-left: 0;
                }
            }

            @media (min-width: 1500px) {
                .window1 {
                    left: calc(15vw); 
                    top: calc(10vh); 
                }

                .window2 {
                    left: calc(50vw); 
                    top: calc(15vh); 
                    transform: translateX(-50%); 
                }

                .window3 {
                    right: calc(18vw); 
                    top: calc(35vh); 
                }
            }
        </style>
    </head>
    <body>
        <div class="window window1">
            <div class="window-title"> 
                Who_We_Are.exe
            </div>

            <p>
                Modernising The Art<br>
                of <i> 'Oops! I Forgot That' </i>
            </p>
        </div>

        <div class="window window2">
            <div class="window-title">HoLoFo_MAeM.exe</div>
            <div class="image-container"> 
                <img src="nobglogo.png" alt="HoLoFo MAeM" class="responsive-image">
            </div>
        </div>

        <div class="window window3">
            <div class="window-title">User_Access.exe</div>
            <center> 
                <h3>Select Your Portal</h3> 
                <button onclick="login('admin')"> 
                    <i class="fas fa-user"></i><br>
                     Admin <br>
                </button>

                <button onclick="login('frontdesk')">
                    <i class="fas fa-building"></i><br>
                     Frontdesk
                </button>

                <button onclick="login('utility')">
                    <i class="fas fa-gear"></i><br>
                     Utility
                </button>
            </center>
        </div>
    </body>
    <script>
        function login(type) {
            // Create a modal to show which login was selected
            const modal = document.createElement('div');
            modal.className = 'fixed';
            modal.innerHTML = `
                <div class="bg-black"></div>
                <div class="modal-content">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Logging in as ${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
                    <p class="text-gray-600 mb-6">You are being redirected to the ${type} portal...</p>
                    <div class="progress-bar-container">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Animate progress bar
            const progressBar = modal.querySelector('.progress-bar');
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        modal.remove();
                        // Debugging log
                        console.log(`Redirecting to ${type.charAt(0).toUpperCase() + type.slice(1)} login page.`);
                        if (type === 'admin') {
                            // Redirect to AdminLogin.html
                            window.location.href = 'AdminLogin.php'; 
                        } else if (type === 'frontdesk') {
                            // Redirect to FrontdeskLogin.html
                            window.location.href = 'FrontdeskLogin.php'; 
                        } else if (type === 'utility') {
                            // Redirect to UtilityLogin.html
                            window.location.href = 'UtilityLogin.php'; 
                        } else {
                            alert(`Welcome to the ${type.charAt(0).toUpperCase() + type.slice(1)} Portal!`);
                        }
                    // Delay before redirection
                    }, 500); 
                } else {
                    width += 2;
                    progressBar.style.width = width + '%';
                }
            }, 30);
        }
    </script>
</html>
