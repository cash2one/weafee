#include <stdio.h>
#include <stdlib.h>
#include <syslog.h>
#include <errno.h>
#include <pthread.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <semaphore.h> 
#include <sys/time.h>
#include <fcntl.h>
#include <unistd.h>
#include <string.h>
#include <uci.h>

#include "monitor.h"
#include "monitor_util.h"
#include "safe.h"

t_ctrl_server_url *ctrl_svr = NULL;
static struct uci_context * ctx = NULL;
char *ctrl_ip_selected = NULL;
char *ctrlsvr_hostname = NULL;
char hjver[33] =  {0};
char ctrlver[33] = {0};
int debug_lvl = 0;

char kversion[32] = {0};
char fversion[32] = {0};
char mversion[32] = {0};
char hw_model[32] = {0};

void 
restart_koala(void)
{
    execute("killall -15 koala", 0);
    sleep(2);
    //execute("chmod +x /etc/koala/koala", 0);
    chmod("/usr/bin/koala", S_IRUSR|S_IWUSR|S_IXUSR);
    sleep(1);
    execute("/usr/bin/koala", 0);
}

void 
start_koala(void)
{
    chmod("/usr/bin/koala", S_IRUSR|S_IWUSR|S_IXUSR);
    execute("/usr/bin/koala", 0);
    sleep(2);
}

void 
config_ctrl_delete(t_ctrl_server_url *node)
{
    t_ctrl_server_url * ptr;
    t_ctrl_server_url * p = NULL;
    
    ptr = ctrl_svr;
    
    if (ptr == NULL) {
        debug(LOG_ERR,"Node list empty!");
    } else if (ptr == node) {
        ctrl_svr = ptr->next;
        if(node->ctrl_url != NULL)
            free(node->ctrl_url);
        free(node);
    } else {
        /* Loop forward until we reach our point in the list. */
        while (ptr->next != NULL && ptr->next != node) {
            ptr = ptr->next;
        }
        /* If we reach the end before finding out element, complain. */
        if (ptr->next == NULL) {
            debug(LOG_ERR, "Node to delete could not be found.");
        /* Free element. */
        } else {
            ptr->next = node->next;
            if(node->ctrl_url != NULL)
                free(node->ctrl_url);
            free(node);
        }
    }
}

void
config_ctrl_clear(void)
{
    t_ctrl_server_url *ptr;
    
    ptr = ctrl_svr;
    while(NULL != ptr){
        config_ctrl_delete(ptr);
        ptr = ptr->next;
    }
    ctrl_svr = NULL;
}

void 
config_read(void)
{
    struct uci_package * p = NULL;
    struct uci_element *s=NULL, *o=NULL, *i=NULL;
    t_ctrl_server_url *h = NULL;
    char * url = NULL;
    
    ctx = uci_alloc_context(); 
    if (0 != uci_load(ctx, UCI_CONFIG_FILE, &p))
        goto cleanup;
    
    uci_foreach_element(&p->sections, s) {
        struct uci_section *sec = uci_to_section(s);
		uci_foreach_element(&sec->options, o) {
			struct uci_option *opt = uci_to_option(o);
			switch(opt->type) {
			case UCI_TYPE_STRING:
                break;
			case UCI_TYPE_LIST:
				uci_foreach_element(&opt->v.list, i) {
				    if(0 == strcmp(opt->e.name, "AuthServerAddr")){
                        url = safe_strdup(i->name);
                        debug(LOG_DEBUG, "updating ctrl url address [%s]", url);
                        if (ctrl_svr == NULL) {
                    		ctrl_svr = safe_malloc(sizeof(t_ctrl_server_url));
                    		ctrl_svr->ctrl_url = safe_strdup(url);
                    		ctrl_svr->next = NULL;
                    	}
                    	else {
                    		/* Advance to the last entry */
                    		for (h = ctrl_svr; h->next != NULL; h = h->next);
                    		h->next = safe_malloc(sizeof(t_ctrl_server_url));
                    		h = h->next;
                    		h->ctrl_url = safe_strdup(url);
                    		h->next = NULL;
                	    }
    			    }
                }
				break;
			default:
				printf("\t# unknown type for option '%s'\n", opt->e.name);
				break;
			}
		}
    }
cleanup:
    uci_free_context(ctx);
    ctx = NULL;    
}
#define CHECK_PING_MS 1900
#define CHECK_PING_GOOD 500

int 
check_ip(char *ip)
{
    int ping_ms=0, ping2_ms=0, ping3_ms=0;
    ping(ip, 2000, &ping_ms);
    debug(LOG_DEBUG, "ping end use %d ms", ping_ms);
    if(ping_ms > CHECK_PING_MS){
        ping(ip, 2000, &ping2_ms); //ping again
        if(ping2_ms > CHECK_PING_MS){
            ping(ip, 2000, &ping3_ms); //ping again  
            if(ping3_ms < CHECK_PING_MS){
                if(ping_ms < CHECK_PING_GOOD)
                    return 1;
            }
        }else{
            if(ping_ms < CHECK_PING_GOOD)
                return 1;
        }
    }else{
        if(ping_ms < CHECK_PING_GOOD)
            return 1;
    } 
    return 0;
}

int 
connect_ctrl_server(void)
{
    t_ctrl_server_url *q = NULL;
    int sockfd;
    struct in_addr *h_addr;
    struct sockaddr_in their_addr;
    int ping_ms = 0;
    
    ctrl_ip_selected = NULL;
    
    for (q = ctrl_svr; q != NULL; q = q->next){
        h_addr = wd_gethostbyname(q->ctrl_url);
        if(!h_addr){
            continue;
        }else{
            debug(LOG_DEBUG, "ctrl_url:%s[%s]", q->ctrl_url, inet_ntoa(*h_addr));
            if(1 == check_ip(inet_ntoa(*h_addr))){
                ctrl_ip_selected = safe_strdup(inet_ntoa(*h_addr));
                ctrlsvr_hostname = safe_strdup(q->ctrl_url);
                break;
            }else{
                continue;
            }
        }
    }
    if(ctrl_ip_selected == NULL){
        return DNS_ERR;
    }
    
    debug(LOG_DEBUG, "Connecting to ctrl server %s", ctrl_ip_selected);
	their_addr.sin_family = AF_INET;
	their_addr.sin_port = htons(80);
	their_addr.sin_addr.s_addr = inet_addr(ctrl_ip_selected);
	memset(&(their_addr.sin_zero), '\0', sizeof(their_addr.sin_zero));
	if ((sockfd = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
		debug(LOG_ERR, "Failed to create a new SOCK_STREAM socket: %s", strerror(errno));
		return(SOCKET_ERR);
	}
	if (connect(sockfd, (struct sockaddr *)&their_addr, sizeof(struct sockaddr)) == -1) {
		/*
	     * Failed to connect
		 * Mark the server as bad and try the next one
		 */
		debug(LOG_ERR, "Failed to connect to auth server %s (%s). Marking it as bad and trying next if possible", ctrl_ip_selected, strerror(errno));
		close(sockfd);
		return(CONNECTED_FAILED);
	}
	else {
		/*
		 * We have successfully connected
		 */
		debug(LOG_DEBUG, "Successfully connected to auth server %s", ctrl_ip_selected);
		return sockfd;
	}
    
}

void
execute_download(char * path)
{
    char *cmd = NULL;
    int rc;

	safe_asprintf(&cmd, "wget -q %s/koala -O /usr/bin/koala_new", path);

    rc = execute(cmd, 0);
    if (rc!=0) {
        debug(LOG_ERR, "get koala failed(%d): %s", rc, cmd);
        return;
	}
	free(cmd);

	rename("/usr/bin/koala_new", "/usr/bin/koala");
}

void 
update_koala(char * r)
{
    char * path = NULL;
    char *cmd = NULL;
    int rc;
    
    char version[VERSION_SIZE] = {0};
    char d_version[VERSION_SIZE] = {0};
    char version_tmp[VERSION_SIZE] = {0};
    
    FILE * fh;
    
    path = safe_strdup(r);
    path += strlen("Update:koala:");
    
    /*step 1: get u version*/
    /*read u version*/
    memset(version_tmp, 0, VERSION_SIZE);
	if ((fh = fopen(KOALA_VERSION_PATH, "r"))) {
		fread(version_tmp, VERSION_SIZE, 1, fh);
		fclose(fh);
		strncpy(version, version_tmp, strlen(version_tmp));
	}else{
	    memset(version, 0, VERSION_SIZE);
	}
	
	/*setp 2: get download server version*/
	safe_asprintf(&cmd, "wget -q %s/version -O /var/version", path);
    rc = execute(cmd, 0);
    if (rc!=0) {
        debug(LOG_ERR, "get d_version failed(%d): %s", rc, cmd);
        return;
	}
	free(cmd);

	if ((fh = fopen("/var/version", "r"))) {
	    memset(version_tmp, 0, VERSION_SIZE);
		fread(version_tmp, VERSION_SIZE, 1, fh);
		fclose(fh);
		strncpy(d_version, version_tmp, strlen(version_tmp));
	}
	
	/*step 3: cmp u_version & d_version*/
    if(strcmp(version, d_version) == 0){
        debug(LOG_DEBUG, "versiom same, return");
        return;
    }else{
        debug(LOG_DEBUG, "versiom dif");
        debug(LOG_DEBUG, "version %s", version);
        debug(LOG_DEBUG, "d_version %s", d_version);
        
        if ((fh = fopen(KOALA_VERSION_PATH, "w+"))) {
    		fwrite(d_version, strlen(d_version), 1, fh);
    		fclose(fh);
    	}    
        debug(LOG_DEBUG, "change version ok!!!");
    }
    /*step 4: execute download files*/
    execute_download(path);
    
}

void 
update_conf(char * r)
{
    if(strstr(r, "koala:") != NULL)
        update_koala(r);
    else
        return;
    return;
}

int 
download_koala(char *from)
{
    int	sockfd, nfds, done;
    fd_set readfds;
    FILE *fh;
    ssize_t	numbytes;
    size_t totalbytes;
    char *gw_mac = NULL;
    unsigned long int sys_uptime  = 0;
    char kver[10] = {0};
    char request[MAX_BUF] = {0};
    struct timeval timeout;
    /*step1: get ctrl server url*/
    config_read();  
    /*step2: connect to ctrl server ip address*/
    sockfd = connect_ctrl_server();
    if(sockfd < 0){
        return sockfd;
    }
    /*step3: ping*/
    if ((gw_mac = get_iface_mac(LOCAL_INT)) == NULL) {
		debug(LOG_DEBUG, "Could not get MAC address information, return...");
		return;
	}
	if ((fh = fopen("/proc/uptime", "r"))) {
		fscanf(fh, "%lu", &sys_uptime);
		fclose(fh);
	}
	
	snprintf(request, sizeof(request) - 1,
			"GET /koala/ping/?gw_mac=%s&hw=%s&start=%lu&kver=%s&mver=%s&fver=%s&hw=%s&from=%s HTTP/1.0\r\n"
			"User-Agent: monitor %s\r\n"
			"Host: %s\r\n"
			"\r\n",
			gw_mac,
			HW_MODULE,
			sys_uptime,
			kversion,
			mversion,
			fversion,
			hw_model,
			from,
			M_VERSION,
			ctrlsvr_hostname);
    
	debug(LOG_DEBUG, "HTTP Request to Server: [%s]", request);
	
	send(sockfd, request, strlen(request), 0);

	debug(LOG_DEBUG, "Reading response");
	
	numbytes = totalbytes = 0;
	done = 0;
	do {
		FD_ZERO(&readfds);
		FD_SET(sockfd, &readfds);
		timeout.tv_sec = 10; /* XXX magic... 10 second */
		timeout.tv_usec = 0;
		nfds = sockfd + 1;

		nfds = select(nfds, &readfds, NULL, NULL, &timeout);

		if (nfds > 0) {
			/** We don't have to use FD_ISSET() because there
			 *  was only one fd. */
			numbytes = read(sockfd, request + totalbytes, MAX_BUF - (totalbytes + 1));
			if (numbytes < 0) {
				debug(LOG_ERR, "An error occurred while reading from auth server: %s", strerror(errno));
				/* FIXME */
				close(sockfd);
				return;
			}
			else if (numbytes == 0) {
				done = 1;
			}
			else {
				totalbytes += numbytes;
				debug(LOG_DEBUG, "Read %d bytes, total now %d", numbytes, totalbytes);
			}
		}
		else if (nfds == 0) {
			debug(LOG_ERR, "Timed out reading data via select() from auth server");
			close(sockfd);
			return;
		}
		else if (nfds < 0) {
			debug(LOG_ERR, "Error reading data via select() from auth server: %s", strerror(errno));
			close(sockfd);
			return;
		}
	} while (!done);
	close(sockfd);

	debug(LOG_DEBUG, "Done reading reply, total %d bytes", totalbytes);

	request[totalbytes] = '\0';

	debug(LOG_DEBUG, "HTTP Response from Server: [%s]", request);
	
	if (strstr(request, "pong") != 0) {
		debug(LOG_DEBUG, "Auth Server Says: Pong");
		/* FIXME */
	}else if (strstr(request, "Update:") != 0){
	    update_conf(strstr(request, "Update:"));
	}else{
		debug(LOG_DEBUG, "Auth server did NOT say pong!");
	}

	return;	
    
}

int 
check_wan(char *host)
{
    struct in_addr *h_addr;
    h_addr = wd_gethostbyname(host);;
    if (!h_addr) {
		debug(LOG_ERR, "Can't resolve %s to IP address", host);
		return 0;
	}else{
	    if(1 == check_ip(inet_ntoa(*h_addr))){
            return 1;
        }else{
            return 0;   
        }
	}
}

void get_version(void)
{
    FILE *fh;
    if ((fh = fopen("/etc/version/kversion", "r"))) {
		fscanf(fh, "%s", kversion);
		fclose(fh);
	}
	if ((fh = fopen("/etc/version/fversion", "r"))) {
		fscanf(fh, "%s", fversion);
		fclose(fh);
	}
	if ((fh = fopen("/etc/version/mversion", "r"))) {
		fscanf(fh, "%s", mversion);
		fclose(fh);
	}
	if ((fh = fopen("/etc/version/hw_model", "r"))) {
		fscanf(fh, "%s", hw_model);
		fclose(fh);
	}
}

void main(void)
{   
    sem_t *sem_reboot = NULL;
    int wait_flag;
    static int wait_sec = 0;
    static int wait_check_koala_sec = 0;
    int pid=0;
    
    get_version();
    
    if(sem_unlink(KOALA_REBOOT) < 0){
        perror(KOALA_REBOOT);
    }
    
    sem_reboot=sem_open(KOALA_REBOOT, O_CREAT|O_EXCL, 0644, 1);
    if(sem_reboot == NULL)
       perror(KOALA_REBOOT);
    
    /*step1: check internet, ping www.baidu.com*/
    while(!check_wan("www.baidu.com")){
        sleep(3);   
    }
    /*step2: check koala version and update koala*/
    download_koala("monitor_normal");
    /*step3: start koala*/
    start_koala();
    /*step4: while 1, check koala stat*/ 
    while(1){
        wait_flag = sem_trywait(sem_reboot);
        if (wait_flag < 0){ 
            if(wait_sec > 5){
                wait_sec = 0;
                debug(LOG_DEBUG, "koala reboot, restart koala..");
                restart_koala();
            }else{
                sleep(1);
                wait_sec++; 
                continue;  
            } 
        }
        sem_post(sem_reboot);
        wait_sec = 0;

        /*check koala pid*/
        wait_check_koala_sec ++;
        if(wait_check_koala_sec == 60/10){ /*1 min*/
            wait_check_koala_sec = 0;
            pid = GetPidByName("/usr/bin/koala");
            if(pid <= 0){
                debug(LOG_ERR, "koala fork = %d", pid);
                download_koala("monitor_err");
                restart_koala();
            }else{
                debug(LOG_DEBUG, "koala stat ok, pid:%d", pid);
            }
        }
        sleep(10);
    }
    
}

