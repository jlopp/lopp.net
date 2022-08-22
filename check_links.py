import os
from fnmatch import fnmatch
import bs4
from requests import get
from tqdm import tqdm
import webbrowser
from fake_headers import Headers
from random import shuffle

# Gets script location. Assumes the script is in the lopp.net folder.
website_directory = os.path.dirname(os.path.realpath(__file__))
os.chdir(website_directory)

# Create a list of all the HTML files in lopp.net
all_html_files = []
for root, dirs, files in os.walk(os.getcwd()):
    for file in files:
        if fnmatch(file, "*.html"):
            all_html_files.append(os.path.join(root, file))

# Parse each HTML and create a list of links associated with each HTML file
all_links = []
for html_file in all_html_files:
    with open(html_file, "r") as f:
        soup = bs4.BeautifulSoup(f, "html.parser")
        for link in soup.find_all("a"):
            all_links.append(link.get("href"))

# Remove all duplicate links and those pointing to other pages in lopp.net
print(f"Total number of links before processing: {len(all_links)}")
all_links = list(set(all_links))  # Removes duplicate links
shuffle(
    all_links
)  # We don't want to visit the same page twice in a row, so shuffle the list

# For some reason, not all the links are removed in one pass so we keep doing it until we've actually removed all the unwanted links
for i in range(5):
    for link in all_links:
        if link[:4] != "http":
            # If the link is not a valid URL, remove it
            all_links.remove(link)
        elif link.find("lopp.net") != -1:
            # Ignores the link if it points to one of the other pages in lopp.net or blog.lopp.net
            all_links.remove(link)

print(f"Total number of links after processing: {len(all_links)}")

# Iterate over each link and download the page with requests
failed_links = []
headers = Headers(headers=True).generate()

# For this first iteration, the timeout is set to 3 seconds
for link in tqdm(all_links):
    try:
        r = get(link, timeout=3, headers=headers)

        if r.status_code != 200:
            failed_links.append(link)

    except:
        failed_links.append(link)

print("Finished checking links with a timeout of 3 seconds")
print(f"Number of failed links: {len(failed_links)}")
print("Retrying the failed links with a timeout of 10 seconds")

# Retries the failed links with a longer timeout
for link in tqdm(failed_links):
    try:
        r = get(link, timeout=10, headers=headers)

        if r.status_code == 200:
            failed_links.remove(link)

    except:
        pass

print("Finished checking links with a timeout of 10 seconds")
print(f"Number of failed links: {len(failed_links)}")

really_failed_links = []

for link in failed_links:
    webbrowser.open_new_tab(link)
    if input("Is this link working?[y]/n ") == "n":
        really_failed_links.append(link)

# Search all the HTML files for the failed links and print them out
files_with_failed_links = []
for html_file in all_html_files:
    with open(html_file, "r") as f:
        soup = bs4.BeautifulSoup(f, "html.parser")
        for link in soup.find_all("a"):
            if link.get("href") in really_failed_links:
                files_with_failed_links.append(f"{html_file} - {link.get('href')}")
                break

# Finally, output a list of the really broken links and their associated HTML files to a text file
os.chdir("..")
try:
    f = open("broken_links.txt", "x")
except:
    f = open("broken_links.txt", "w")

for link in files_with_failed_links:
    f.write(link + "\n")

f.close()
