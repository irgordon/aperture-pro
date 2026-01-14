import subprocess
import os

# Set the WordPress environment variable for the script
os.environ['WP_ENV'] = 'development'

# Command to execute the PHP script
command = ["php", "tests/verify_printful_cache.php"]

try:
    # Execute the command
    result = subprocess.run(
        command,
        capture_output=True,
        text=True,
        check=True
    )

    # Print the output from the PHP script
    print("Output from PHP script:")
    print(result.stdout)

    # You would then check your debug.log for the 'error_log' messages.
    # For this simulation, we'll just confirm the script ran.

except FileNotFoundError:
    print("Error: 'php' command not found. Is PHP installed and in your PATH?")
except subprocess.CalledProcessError as e:
    print(f"An error occurred while executing the PHP script:")
    print(f"Return code: {e.returncode}")
    print(f"Output: {e.stdout}")
    print(f"Error output: {e.stderr}")
except Exception as e:
    print(f"An unexpected error occurred: {e}")
