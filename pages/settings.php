<?php $config = get_config(); ?>

<div class="page-header">
    <h1>Settings</h1>
</div>

<div class="form-card">
    <h2 class="settings-section-title"><i class="fas fa-key"></i> Change Password</h2>

    <form id="change-password-form" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save New Password
            </button>
        </div>
    </form>
    <div id="settings-message" class="form-message hidden"></div>
</div>
