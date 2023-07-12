import argparse
import os
import webbrowser
from fnmatch import fnmatch
from random import shuffle
from time import sleep
from typing import Dict
from urllib.parse import urlparse

import bs4
from fake_headers import Headers
from requests import get
from tqdm.asyncio import tqdm

parser = argparse.ArgumentParser(description="Check links in a directory")
parser.add_argument(
    "-d",
    "--directory",
    help="Directory to check",
    default=os.path.dirname(os.path.realpath(__file__)),
)
parser.add_argument(
    "-f",
    "--fast",
    action="store_true",
    help="Fast async mode",
)
parser.add_argument(
    "-ff",
    "--superfast",
    action="store_true",
    help="Super fast mode (may get blocked by sites)",
)

args = parser.parse_args()

# Gets script location. Assumes the script is in the lopp.net folder.
os.chdir(args.directory)

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
)  # Shuffles the list so we don't hit the same website twice in a row and get blocked

# For some reason, not all the links are removed in one pass so we keep doing it until we've actually removed all the unwanted links
for i in range(5):
    for link in all_links:
        if link is None:
            continue
        if link[0] == "#":
            # ignore anchor links
            all_links.remove(link)
        elif link[:4] != "http":
            # If the link is not a valid URL, remove it
            all_links.remove(link)
        elif link.find("lopp.net") != -1:
            # Ignores the link if it points to one of the other pages in lopp.net or blog.lopp.net
            all_links.remove(link)
        elif link.find(".onion") != -1:
            # Ignores the link if it is a tor address
            all_links.remove(link)

print(f"Total number of links after processing: {len(all_links)}")

# Iterate over each link and download the page
failed_links = []
headers = Headers(headers=True).generate()

if args.fast or args.superfast:
    import asyncio

    import aiohttp

    sorted_links: Dict = {}

    # Create the top level domain list
    for link in all_links:
        domain = urlparse(link).netloc

        if domain not in sorted_links:
            sorted_links[domain] = []

    # Add the links to the sorted_links dict
    for link in all_links:
        domain = urlparse(link).netloc
        sorted_links[domain].append(link)

    # Find the length of the longest list
    length = 0
    for domain, links in sorted_links.items():
        length = len(links) if len(links) > length else length  # I❤️ oneliners

    # Get the first link from each domain
    set_of_links = []
    counter = 0
    for i in range(length):
        print(f"Counter: {counter}")
        for domain in sorted_links:
            if counter < len(sorted_links[domain]):
                if (
                    sorted_links[domain][counter] not in set_of_links
                    and sorted_links[domain][counter][:4] == "http"
                ):
                    set_of_links.append(sorted_links[domain][counter])
        counter += 1

        # print(set_of_links)

        async def get_resp(session, url):
            try:
                async with session.get(url) as response:
                    print(url)
                    if response.status != 200:
                        failed_links.append(url)
                    await response.text()
            except Exception as e:
                print(e)
                failed_links.append(url)

        async def download_links():
            timeout = aiohttp.ClientTimeout(total=10)

            async with aiohttp.ClientSession(
                headers=headers, timeout=timeout
            ) as session:
                tasks = []
                for link in set_of_links:
                    task = asyncio.create_task(get_resp(session, link))
                    tasks.append(task)
                await asyncio.gather(*tasks)

        # Run the async function
        asyncio.run(download_links())

        # Clear the set of links
        set_of_links = []

        # Pause for 3 seconds
        if not args.superfast:
            sleep(3)

else:
    for link in tqdm(all_links):
        try:
            r = get(link, headers=headers, timeout=10)
            if r.status_code != 200:
                failed_links.append(link)
        except Exception as e:
            print(e)
            failed_links.append(link)

print(f"Number of failed links: {len(failed_links)}")

really_failed_links = []

for link in failed_links:
    webbrowser.open_new_tab(link)
    print(link)
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
with open("broken_links.txt", "w+") as f:
    for link in files_with_failed_links:
        f.write(link + "\n")
