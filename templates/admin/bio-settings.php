<div class="wrap ap-bio-settings">
    <h1 class="wp-heading-inline">Link In Bio Configuration</h1>
    <hr class="wp-header-end">

    <div id="ap-bio-loading" style="margin-top: 20px;">Loading settings...</div>

    <form id="ap-bio-form" style="display: none; max-width: 800px; margin-top: 20px;">

        <!-- Profile Section -->
        <div class="card">
            <h2>Profile</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bio-profile-image">Profile Image URL</label></th>
                    <td>
                        <input name="profileImage" type="url" id="bio-profile-image" class="regular-text" placeholder="https://...">
                        <p class="description">URL to your profile picture.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-name">Display Name</label></th>
                    <td>
                        <input name="name" type="text" id="bio-name" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-description">Bio / Description</label></th>
                    <td>
                        <textarea name="description" id="bio-description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Appearance Section -->
        <div class="card" style="margin-top: 20px;">
            <h2>Appearance</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bio-primary-color">Primary Color</label></th>
                    <td>
                        <input name="primaryColor" type="color" id="bio-primary-color" value="#0073aa">
                        <p class="description">Sets the primary color for buttons and links.</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Links Section -->
        <div class="card" style="margin-top: 20px;">
            <h2>Links</h2>
            <div id="bio-links-container">
                <!-- Links injected here by JS -->
            </div>
            <button type="button" class="button" id="add-bio-link">Add Link</button>
        </div>

        <!-- Modules Section -->
        <div class="card" style="margin-top: 20px;">
            <h2>Modules</h2>

            <h3>Donation</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Donation</th>
                    <td>
                        <label>
                            <input name="donationEnabled" type="checkbox" id="bio-donation-enabled">
                            Show Donation Button/Form
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-donation-link">Donation Link/URL</label></th>
                    <td>
                        <input name="donationLink" type="url" id="bio-donation-link" class="regular-text">
                        <p class="description">Link to PayPal, Stripe, etc.</p>
                    </td>
                </tr>
            </table>

            <h3>Shop (Printful)</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Shop</th>
                    <td>
                        <label>
                            <input name="shopEnabled" type="checkbox" id="bio-shop-enabled">
                            Show 2x2 Shop Grid
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-printful-key">Printful API Key</label></th>
                    <td>
                        <input name="printfulKey" type="password" id="bio-printful-key" class="regular-text">
                        <p class="description">If empty, mock products will be displayed.</p>
                    </td>
                </tr>
            </table>

            <h3>Social Icons</h3>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="bio-social-facebook">Facebook URL</label></th>
                    <td><input name="socials[facebook]" type="url" id="bio-social-facebook" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-social-instagram">Instagram URL</label></th>
                    <td><input name="socials[instagram]" type="url" id="bio-social-instagram" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-social-youtube">YouTube URL</label></th>
                    <td><input name="socials[youtube]" type="url" id="bio-social-youtube" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bio-social-500px">500px URL</label></th>
                    <td><input name="socials[500px]" type="url" id="bio-social-500px" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="button button-primary button-hero" id="bio-save-btn">Save Changes</button>
        </div>
    </form>
</div>

<template id="tmpl-bio-link-row">
    <div class="bio-link-row" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; background: #fff;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="flex: 1;">
                <label>Label: <input type="text" class="widefat bio-link-label"></label>
            </div>
            <div style="flex: 1;">
                <label>URL: <input type="url" class="widefat bio-link-url"></label>
            </div>
            <div style="flex: 0 0 100px;">
                 <label>Icon: <input type="text" class="widefat bio-link-icon" placeholder="fa-icon"></label>
            </div>
            <button type="button" class="button bio-remove-link" style="color: #a00; border-color: #a00;">&times;</button>
        </div>
        <div style="margin-top: 5px;">
            <label>Thumbnail URL: <input type="url" class="widefat bio-link-thumb"></label>
        </div>
    </div>
</template>
