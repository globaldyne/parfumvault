<?php 
if (getenv('PASS_RESET_INFO') !== "DISABLED") {
    if ($system_settings['EMAIL_isEnabled'] == 1) { ?>
        <!-- Forgot Password Modal -->
        <div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgot_pass_label">Forgot Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="forgot_msg"></div>
                        <div class="form-floating mb-3" id="forgot_email_form">
                            <input type="email" class="form-control" id="forgot_email" placeholder="name@example.com">
                            <label for="forgot_email">Email address</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="forgot_submit">Reset Password</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- Forgot Password Info -->
        <div class="modal fade" id="forgot_pass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="forgot_pass_label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgot_pass_label">Forgot Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if ($conn->query("SELECT id FROM users LIMIT 1")->num_rows == 0) { ?>
                            <p>When you first installed <strong><?=$product?></strong>, you were prompted to set a password...</p>
                            <!-- Additional instructions based on platform -->
                        <?php } else { ?>
                            <p>If you have forgotten your password, please contact your system administrator for assistance.</p>
                        <?php } ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>
