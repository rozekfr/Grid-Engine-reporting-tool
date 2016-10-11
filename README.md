# Grid Engine reporting tool

This is a project created as bachelor thesis on Faculty of information technology on Brno university of technology.

## README
This is file with information about the source files of this application.

### BACK END FILES
- accounting_parser.php       - used to process accounting data file
- create_rrd_databases.php 	  - used to create RRD database files, which are necessary needed to run the application
- get_gpu_info.php 			      - used to get information about GPUs in each node
- get_groups.php              - used to get groups and their nodes
- get_job_info_checker.sh		  - used to periodic check if somebody requested information about job
- get_job_info_request.php	  - used to send job data request
- get_job_info_response.php	  - used to get job data response from request
- pending_job_parser.php		  - used to process pending jobs
- pending_jobs_updater.sh		  - used to periodic update of pending jobs
- qstat_parser.php			      - used to process qstat file
- resource_list_checker.sh 	  - used to periodic check if somebody requsted information about blocking jobs
- resource_list_request.php	  - used to send resource list request
- resource_list_response.php	- used to get resource list response
- rrd_update.php				      - used to update RRD databases (files)
- update_rrd.sh 				      - used to get current data from cluster and periodic start of rrd_update.php script

### FRONT END FILES
- cluster.php
- cluster_header.php
- cluster_settings.php
- content.php
- functions.php
- header.php
- index.php
- loading.html
- menu.php
- pripojeniDB.php
- prostredky.php
- prostredky_header.php
- prostredky_podmenu.php
- prostredky_settings.php
- settings.php
- ulohy.php
- ulohy_ajax.php
- ulohy_cekajici_ajax.php
- ulohy_efektivita_ajax.php
- ulohy_header.php
- ulohy_settings.php
- uzivatele.php
- uzivatele_ajax.php
- uzivatele_header.php
- uzivatele_settings.php
