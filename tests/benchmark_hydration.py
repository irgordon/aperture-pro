
import os
import psutil
import sys

# Add a mock WP_Post class to simulate the memory footprint of a WordPress post object
class MockWP_Post:
    def __init__(self, id):
        self.ID = id
        self.post_author = '1'
        self.post_date = '2023-10-27 10:00:00'
        self.post_date_gmt = '2023-10-27 10:00:00'
        self.post_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' * 5
        self.post_title = 'Test Post Title ' + str(id)
        self.post_excerpt = ''
        self.post_status = 'publish'
        self.comment_status = 'closed'
        self.ping_status = 'closed'
        self.post_password = ''
        self.post_name = 'test-post-' + str(id)
        self.to_ping = ''
        self.pinged = ''
        self.post_modified = '2023-10-27 10:00:00'
        self.post_modified_gmt = '2023-10-27 10:00:00'
        self.post_content_filtered = ''
        self.post_parent = 0
        self.guid = 'http://example.com/?p=' + str(id)
        self.menu_order = 0
        self.post_type = 'post'
        self.post_mime_type = ''
        self.comment_count = '0'
        self.filter = 'raw'

def get_memory_usage():
    """Returns the RSS memory usage of the current process in bytes."""
    process = psutil.Process(os.getpid())
    return process.memory_info().rss

def benchmark_hydration(num_posts):
    """
    Simulates fetching N full WP_Post objects and measures memory.
    """
    start_mem = get_memory_usage()
    # In a real PHP scenario, this would be an array of WP_Post objects
    # We are simulating this by creating N mock objects
    images = [MockWP_Post(i) for i in range(num_posts)]
    end_mem = get_memory_usage()
    # Ensure the list is not garbage collected before memory is measured
    len(images)
    return end_mem - start_mem

def benchmark_ids(num_posts):
    """
    Simulates fetching N post IDs and measures memory.
    """
    start_mem = get_memory_usage()
    # This simulates fetching only the IDs, which would be an array of integers
    image_ids = [i for i in range(num_posts)]
    end_mem = get_memory_usage()
    len(image_ids)
    return end_mem - start_mem

def main():
    num_posts = 10000  # A realistic number for a large gallery
    print(f"--- Hydration Benchmark (Simulating {num_posts} images) ---")

    # --- Baseline: Full Object Hydration ---
    mem_hydration = benchmark_hydration(num_posts)
    print(f"Memory for Hydrated Objects: {mem_hydration / 1024:.2f} KB")

    # --- Optimized: IDs only ---
    mem_ids = benchmark_ids(num_posts)
    print(f"Memory for IDs only:         {mem_ids / 1024:.2f} KB")

    # --- Comparison ---
    if mem_ids > 0:
        reduction = mem_hydration - mem_ids
        reduction_percent = (reduction / mem_hydration) * 100
        print(f"\\nMemory Reduction: {reduction / 1024:.2f} KB ({reduction_percent:.1f}%)")
    else:
        print("\\nCould not calculate reduction.")

if __name__ == "__main__":
    main()
