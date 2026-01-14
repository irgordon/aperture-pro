<?php
/**
 * Front Page Template for Aperture Pro Studio Theme
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

        <section class="hero">
            <div class="container">
                <h1>Visuals that <br><span class="text-primary">Demand Attention.</span></h1>

                <p class="hero-text">
                    Whether you are looking to capture a bold portrait, an infusion of energy from the streets, or a look into everyday life, you’ve come to the right place. Photography is about picture-taking; it is about capturing those truly unique moments in time and preserving memories before they fade. Each shoot is a chance to etch moments of laughter, love, and pure emotion in time. I am committed to creating images that are full of life, genuine, and unlike any other, so that every picture brings about a memory, an emotion, or a story. Want to capture images that scream 'homemade' over 'mass-produced'? Let’s put those fleeting moments of time into a memory you can enjoy in pictures. Ready to be in front of the camera?
                </p>

                <div class="mt-2">
                    <a href="#contact" class="btn btn-accent">Book Your Shoot</a>
                    <button onclick="openModal('gallery')" class="btn btn-secondary ml-1">View Portfolio</button>
                </div>
            </div>
        </section>

        <section id="services">
            <div class="container">
                <div class="text-center mb-3">
                    <h2>Unapologetically Authentic</h2>
                    <p>No stiff poses. No plastic smiles. Just high-end imagery tailored to your goals.</p>
                </div>

                <div class="services-grid">
                    <article class="card">
                        <div class="card-decoration bg-primary"></div>
                        <span class="card-icon">👤</span>
                        <h3>Modern Headshots</h3>
                        <p><strong>Your digital handshake.</strong> Dynamic profiles for LinkedIn or corporate branding that build instant trust.</p>
                    </article>

                    <article class="card">
                        <div class="card-decoration bg-accent"></div>
                        <span class="card-icon">📸</span>
                        <h3>Editorial & Street</h3>
                        <p><strong>Grit and glory.</strong> Urban textures of National Landing and DC architecture in high-contrast B&W or vibrant color.</p>
                    </article>

                    <article class="card">
                        <div class="card-decoration bg-primary"></div>
                        <span class="card-icon">✨</span>
                        <h3>Portrait & Boudoir</h3>
                        <p><strong>Empowerment in focus.</strong> A judgment-free experience using natural light to capture your most powerful self.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="vision" class="bg-light-alt">
            <div class="container">
                <div class="about-split">
                    <img src="https://iangordon.pro/images/ian-gordon.jpg"
                         alt="Ian Gordon Photographer in Arlington VA"
                         class="about-img"
                         loading="lazy"
                         width="800" height="500">

                    <div>
                        <h2 class="mb-1-5">The "Local Lens" Advantage</h2>
                        <p>
                            Hi, I'm <strong>Ian Gordon</strong>. I believe the best photos aren't taken in a studio—they're found in the world we live in.
                        </p>
                        <p>
                            Living in the heart of the DMV means I know exactly when the light hits the <strong>Air Force Memorial</strong> perfectly, or which hidden murals in <strong>Crystal City</strong> make for the wildest backdrops.
                        </p>
                        <p>
                            My goal isn't just to press a button. It's to use our shared environment to craft a <strong>visual legacy</strong> for you.
                        </p>
                        <button onclick="openModal('gallery')" class="btn btn-secondary mt-1">View Full Gallery</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="portfolio">
            <div class="container text-center">
                <h2>Recent Captures</h2>
                <p class="mb-2">Real clients. Real locations. Real emotion.</p>

                <div class="portfolio-grid">
                    <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=400&q=80" alt="Arlington Portrait Photography" class="portfolio-item" loading="lazy">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80" alt="Boudoir Photography DC" class="portfolio-item" loading="lazy">
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80" alt="Male Headshot Tysons" class="portfolio-item" loading="lazy">
                </div>

                <div class="mt-3">
                    <button onclick="openModal('gallery')" class="btn btn-accent">View All 50+ Photos</button>
                </div>
            </div>
        </section>

        <section id="contact" class="bg-light">
            <div class="container">
                <div class="text-center mb-2">
                    <h2>Let's Create Something Great</h2>
                    <p>Tell me about your vision. I usually reply within 24 hours.</p>
                </div>

                <div class="contact-form-wrapper">
                    <div id="form-success" class="form-message success">
                        <h3>Message Sent! 🎉</h3>
                        <p>Thanks for reaching out. I'll get back to you at <strong>support@iangordonphotography.com</strong> shortly.</p>
                    </div>
                    <div id="form-error" class="form-message error">
                        <h3>Oops!</h3>
                        <p>Something went wrong. Please try again or email me directly.</p>
                    </div>

                    <form id="bookingForm" name="submit-to-google-sheet">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required placeholder="What should I call you?">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required placeholder="Where can I send the details?">
                        </div>
                        <div class="form-group">
                            <label for="session-type">I'm interested in...</label>
                            <select id="session-type" name="type" required>
                                <option value="" disabled selected>Select a session type...</option>
                                <option value="headshots">Modern Headshots</option>
                                <option value="street">Street / Editorial Branding</option>
                                <option value="portrait">Creative Portrait</option>
                                <option value="boudoir">Boudoir Experience</option>
                                <option value="family">Family / Lifestyle</option>
                                <option value="wedding">Wedding / Elopement</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Vision</label>
                            <textarea id="message" name="message" rows="4" placeholder="Do you have a specific date or location in mind?"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="PASTE_YOUR_NEW_SITE_KEY_HERE"></div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-accent w-full">Send Request</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="locations">
            <div class="container relative z-2">
                <h2>Serving the Entire DMV</h2>
                <div class="tag-cloud">
                    <span class="tag">Arlington VA</span>
                    <span class="tag">Alexandria VA</span>
                    <span class="tag">Washington DC</span>
                    <span class="tag">National Harbor</span>
                    <span class="tag">Navy Yard</span>
                    <span class="tag">Reston</span>
                    <span class="tag">Georgetown</span>
                </div>
            </div>
        </section>

<?php
get_footer();
?>
