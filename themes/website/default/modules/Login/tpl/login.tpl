<div class="container">
    <div class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Username</label>
        <input type="text" id="inputEmail" class="form-control" name="username" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" name="pass" placeholder="Password" required>
        <div class="checkbox">
            <label>
                <input type="checkbox" value="1" name="remember"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        
        <button class="btn btn-lg btn-primary btn-block" type="button" data-toggle="modal" data-target="#myModal">Sign up</button>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="color:red;"><span class="glyphicon glyphicon-edit"></span> Sign Up</h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="usrname"><span class="glyphicon glyphicon-user"></span> Username</label>
                            <input type="text" class="form-control" id="reg_username" name="reg_username" placeholder="Enter username for login">
                        </div>
                        <div class="form-group">
                            <label for="usrname"><span class="glyphicon glyphicon-envelope"></span> Email</label>
                            <input type="text" class="form-control" id="reg_email" name="reg_email" placeholder="Enter email receive alerts">
                        </div>
                        <div class="form-group">
                            <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
                            <input type="password" class="form-control" id="reg_pass" name="reg_pass" placeholder="Enter password">
                        </div>
                        <div class="form-group">
                            <label for="psw"><span class="glyphicon glyphicon-sunglasses"></span> Retype Password</label>
                            <input type="password" class="form-control" id="reg_pass2" name="reg_pass2" placeholder="Retype password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-success" onclick="shop.signup.submit()"><span class="glyphicon glyphicon-check"></span> Sign up</button>
                    <button type="button" class="btn btn-default btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /container -->
