<script>
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '<?php echo Yii::$app->params['FACEBOOK_CLIENT_ID']; ?>',
                    cookie: true,
                    xfbml: true,
                    version: 'v7.0'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <script>
            // Facebook login with JavaScript SDK
            function fbLogin() {
                FB.login(function (response) {
                    if (response.authResponse) {
                        // Get and display the user profile data
                        getFbUserData();
                    } else {
                        document.getElementById('status').innerHTML = 'User cancelled login or did not fully authorize.';
                    }
                }, {scope: 'email'});
            }

            // Fetch the user profile data from facebook
            function getFbUserData() {
                FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
                        function (response) {
                            saveUserData(response);
                            fbLogout();
                        });
            }

            // Logout from facebook
            function fbLogout() {
                FB.logout();
            }

            // Save user data to the database
            function saveUserData(userData) {
                $('input[name="userData"]').val(JSON.stringify(userData));
                $('#fbPost').submit();
            }
        </script>
        <script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>
        <script>
            function onLoadCallback() {
                $('span[id^="not_signed_"]').html('Sign in');
            }

            function onSignIn(googleUser) {
                // Useful data for your client-side scripts:
                var profile = googleUser.getBasicProfile();

                // The ID token you need to pass to your backend:
                var id_token = googleUser.getAuthResponse().id_token;
                //console.log("ID Token: " + id_token);
                var arraydata = {};
                arraydata['id'] = profile.getId();
                arraydata['FullName'] = profile.getName();
                arraydata['GivenName'] = profile.getGivenName();
                arraydata['FamilyName'] = profile.getFamilyName();
                arraydata['ImageURL'] = profile.getImageUrl();
                arraydata['Email'] = profile.getEmail();

                // Save user data
                saveUserDataGoogle(arraydata);
                signOut();

            }
            // Save user data to the database
            function saveUserDataGoogle(userData) {
                $('input[name="userData_google"]').val(JSON.stringify(userData));

                $('#googlePost').submit();
            }

            function signOut() {
                var auth2 = gapi.auth2.getAuthInstance();
                auth2.signOut().then(function () {
                    // console.log('User signed out.');
                });
            }

        </script> 