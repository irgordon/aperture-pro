from playwright.sync_api import sync_playwright
import os

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # Load local HTML file
        cwd = os.getcwd()
        url = f"file://{cwd}/verification/mock-bio.html"
        print(f"Loading {url}...")
        page.goto(url)

        # Verify title
        assert "Link In Bio" in page.title()

        # Verify Donation Logic
        donate_btn = page.locator(".ap-bio-main-btn")
        preset_10 = page.locator(".ap-donate-btn[data-amount='10']")

        # Initial text
        print(f"Initial Button Text: {donate_btn.inner_text()}")
        assert donate_btn.inner_text() == "Donate"

        # Click $10 preset
        print("Clicking $10 preset...")
        preset_10.click()

        # Check update
        print(f"Updated Button Text: {donate_btn.inner_text()}")
        assert donate_btn.inner_text() == "Donate $10"

        # Screenshot
        screenshot_path = f"{cwd}/verification/bio-page.png"
        page.screenshot(path=screenshot_path, full_page=True)
        print(f"Screenshot saved to {screenshot_path}")

        browser.close()

if __name__ == "__main__":
    run()
