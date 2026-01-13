import os
import base64
import psutil
import time

def get_memory_usage():
    process = psutil.Process(os.getpid())
    return process.memory_info().rss

def benchmark_legacy(file_path):
    # Simulate PHP's file_get_contents + base64_encode + payload construction
    start_mem = get_memory_usage()

    with open(file_path, 'rb') as f:
        file_content = f.read()

    # PHP's base64_encode returns a string
    base64_file = base64.b64encode(file_content)

    payload = b""
    boundary = b"--------------------------" + str(time.time()).encode()

    payload += b"--" + boundary + b"\r\n"
    payload += b'Content-Disposition: form-data; name="file"' + b"\r\n\r\n"
    payload += base64_file + b"\r\n"
    payload += b"--" + boundary + b"--\r\n"

    end_mem = get_memory_usage()

    # Hold onto payload so it's not GC'd before measurement
    len(payload)

    return end_mem - start_mem

def benchmark_optimized(file_path):
    # Simulate usage of a file handle / CurlFile
    start_mem = get_memory_usage()

    # In the optimized version, we don't read the whole file.
    # We just open a file object or pass the path.
    # Emulating passing a file handle/path
    f = open(file_path, 'rb')

    post_fields = {
        'file': f,
        'fileName': os.path.basename(file_path),
        'useUniqueFileName': 'false'
    }

    end_mem = get_memory_usage()
    f.close()

    return end_mem - start_mem

def main():
    file_path = "temp_large_file.dat"
    # Create 20MB file
    size_mb = 20
    with open(file_path, 'wb') as f:
        f.write(os.urandom(size_mb * 1024 * 1024))

    print(f"Benchmark with {size_mb}MB file:")

    try:
        mem_diff_legacy = benchmark_legacy(file_path)
        print(f"Legacy Implementation Memory Increase: {mem_diff_legacy / 1024 / 1024:.2f} MB")

        mem_diff_optimized = benchmark_optimized(file_path)
        print(f"Optimized Implementation Memory Increase: {mem_diff_optimized / 1024 / 1024:.2f} MB")

    finally:
        if os.path.exists(file_path):
            os.remove(file_path)

if __name__ == "__main__":
    main()
