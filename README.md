Media Download Tracker logs file downloads via the [Media Entity Download](/project/media_entity_download) route.

### Features

This module records the following information:

*   Date and time
*   Media ID
*   User ID
*   Requested URL
*   Referrer
*   IP address

### Post-Installation

Add a View showing "Media Download Tracker" content and add the relevant fields.

Add relationships to show other properties such as media name or username.

Use aggregation to show total downloads for each media entity.

Export as a CSV file to process the results externally.

### Additional Requirements

[Media Entity Download](/project/media_entity_download)

### Similar projects

[Media Entity Download Count](/project/media_entity_download_count) counts how many times the media item is downloaded and stores the result within the entity itself.