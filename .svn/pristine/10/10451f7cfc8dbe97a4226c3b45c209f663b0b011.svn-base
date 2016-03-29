/********************************************************************\
 * This program is free software; you can redistribute it and/or    *
 * modify it under the terms of the GNU General Public License as   *
 * published by the Free Software Foundation; either version 2 of   *
 * the License, or (at your option) any later version.              *
 *                                                                  *
 * This program is distributed in the hope that it will be useful,  *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of   *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the    *
 * GNU General Public License for more details.                     *
 *                                                                  *
 * You should have received a copy of the GNU General Public License*
 * along with this program; if not, contact:                        *
 *                                                                  *
 * Free Software Foundation           Voice:  +1-617-542-5942       *
 * 59 Temple Place - Suite 330        Fax:    +1-617-542-2652       *
 * Boston, MA  02111-1307,  USA       gnu@gnu.org                   *
 *                                                                  *
 \********************************************************************/

/*
 * $Id: util.c 1429 2009-11-04 14:21:07Z gbastien $
 */
/**
  @file util.c
  @brief Misc utility functions
  @author Copyright (C) 2004 Philippe April <papril777@yahoo.com>
  @author Copyright (C) 2006 Benoit Grégoire <bock@step.polymtl.ca>
 */

#define _GNU_SOURCE

#include <stdio.h>
#include <stdlib.h>
#include <syslog.h>
#include <errno.h>
#include <pthread.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/unistd.h>
#include <netinet/in.h>
#include <sys/ioctl.h>
#include <arpa/inet.h>
#include <netinet/ip.h>
#include <netinet/ip_icmp.h>
#include <sys/time.h>
#include <netinet/in.h>
#include <dirent.h>

#if defined(__NetBSD__)
#include <sys/socket.h>
#include <ifaddrs.h>
#include <net/if.h>
#include <net/if_dl.h>
#include <util.h>
#endif

#ifdef __linux__
#include <netinet/in.h>
#include <net/if.h>
#endif

#include <string.h>
#include <pthread.h>
#include <netdb.h>

#include "client_list.h"
#include "safe.h"
//#include "util.h"
#include "conf.h"
//#include "debug.h"
#include "monitor_util.h"
#include "../config.h"

static pthread_mutex_t ghbn_mutex = PTHREAD_MUTEX_INITIALIZER;

/** Fork a child and execute a shell command, the parent
 * process waits for the child to return and returns the child's exit()
 * value.
 * @return Return code of the command
 */
int
execute(char *cmd_line, int quiet)
{
        int pid,
            status,
            rc;

        const char *new_argv[4];
        new_argv[0] = "/bin/sh";
        new_argv[1] = "-c";
        new_argv[2] = cmd_line;
        new_argv[3] = NULL;

        pid = safe_fork();
        if (pid == 0) {    /* for the child process:         */
                /* We don't want to see any errors if quiet flag is on */
                if (quiet) close(2);
                if (execvp("/bin/sh", (char *const *)new_argv) == -1) {    /* execute the command  */
                        debug(LOG_ERR, "execvp(): %s", strerror(errno));
                } else {
                        debug(LOG_ERR, "execvp() failed");
                }
                exit(1);
        }

        /* for the parent:      */
	debug(LOG_DEBUG, "Waiting for PID %d to exit", pid);
	rc = waitpid(pid, &status, 0);
	debug(LOG_DEBUG, "Process PID %d exited", rc);

        return (WEXITSTATUS(status));
}

	struct in_addr *
wd_gethostbyname(const char *name)
{
	struct hostent *he;
	struct in_addr *h_addr, *in_addr_temp;

	/* XXX Calling function is reponsible for free() */

	h_addr = safe_malloc(sizeof(struct in_addr));

	LOCK_GHBN();

	he = gethostbyname(name);

	if (he == NULL) {
		free(h_addr);
		UNLOCK_GHBN();
		return NULL;
	}

	in_addr_temp = (struct in_addr *)he->h_addr_list[0];
	h_addr->s_addr = in_addr_temp->s_addr;

	UNLOCK_GHBN();

	return h_addr;
}

char *
get_iface_mac(const char *ifname)
{
#if defined(__linux__)
	int r, s;
	struct ifreq ifr;
	char *hwaddr;

	char mac[128] = {0};
	char command[256] = {0};
	FILE *fp = NULL;

	snprintf(command, sizeof(command), "ifconfig %s | grep \"HWaddr\" | awk '{ print $5 }'", ifname);

	fp = popen(command, "r");
	if(NULL == fp)
	{
	  return NULL;
	}
	fgets(mac, sizeof(mac), fp);
	pclose(fp);
	
	mac[strlen(mac)-1] = '\0';
	
	return strdup(mac);

	strcpy(ifr.ifr_name, ifname);

	s = socket(PF_INET, SOCK_DGRAM, 0);
	if (-1 == s) {
		debug(LOG_ERR, "get_iface_mac socket: %s", strerror(errno));
		return NULL;
	}

	r = ioctl(s, SIOCGIFHWADDR, &ifr);
	if (r == -1) {
		debug(LOG_ERR, "get_iface_mac ioctl(SIOCGIFHWADDR): %s", strerror(errno));
		close(s);
		return NULL;
	}

	hwaddr = ifr.ifr_hwaddr.sa_data;
	close(s);
	snprintf(mac, sizeof(mac), "%02X%02X%02X%02X%02X%02X", 
			hwaddr[0] & 0xFF,
			hwaddr[1] & 0xFF,
			hwaddr[2] & 0xFF,
			hwaddr[3] & 0xFF,
			hwaddr[4] & 0xFF,
			hwaddr[5] & 0xFF
		);

	return strdup(mac);
#elif defined(__NetBSD__)
	struct ifaddrs *ifa, *ifap;
	const char *hwaddr;
	char mac[13], *str = NULL;
	struct sockaddr_dl *sdl;

	if (getifaddrs(&ifap) == -1) {
		debug(LOG_ERR, "getifaddrs(): %s", strerror(errno));
		return NULL;
	}
	for (ifa = ifap; ifa != NULL; ifa = ifa->ifa_next) {
		if (strcmp(ifa->ifa_name, ifname) == 0 &&
				ifa->ifa_addr->sa_family == AF_LINK)
			break;
	}
	if (ifa == NULL) {
		debug(LOG_ERR, "%s: no link-layer address assigned");
		goto out;
	}
	sdl = (struct sockaddr_dl *)ifa->ifa_addr;
	hwaddr = LLADDR(sdl);
	snprintf(mac, sizeof(mac), "%02X%02X%02X%02X%02X%02X",
			hwaddr[0] & 0xFF, hwaddr[1] & 0xFF,
			hwaddr[2] & 0xFF, hwaddr[3] & 0xFF,
			hwaddr[4] & 0xFF, hwaddr[5] & 0xFF);

	str = strdup(mac);
out:
	freeifaddrs(ifap);
	return str;
#else
	return NULL;
#endif
}

/****ping block*****/
#define PACKET_SIZE     4096
#define ERROR           0
#define SUCCESS         1

/*两个timeval结构相减*/
void tv_sub(struct timeval *out,struct timeval *in)
{   
    if( (out->tv_usec-=in->tv_usec)<0)
    {   
        --out->tv_sec;
        out->tv_usec+=1000000;
    }
    out->tv_sec-=in->tv_sec;
}

// 效验算法
unsigned short cal_chksum(unsigned short *addr, int len)
{
    int nleft=len;
    int sum=0;
    unsigned short *w=addr;
    unsigned short answer=0;
    
    while(nleft > 1)
    {
           sum += *w++;
        nleft -= 2;
    }
    
    if( nleft == 1)
    {       
        *(unsigned char *)(&answer) = *(unsigned char *)w;
           sum += answer;
    }
    
    sum = (sum >> 16) + (sum & 0xffff);
    sum += (sum >> 16);
    answer = ~sum;
    
    return answer;
}

// Ping函数
int ping( char *ips, int timeout, int *ping_ms)
{
    struct timeval timeo;
    int sockfd;
    struct sockaddr_in addr;
    struct sockaddr_in from;
    
    struct timeval *tval;
    struct ip *iph;
    struct icmp *icmp;

    char sendpacket[PACKET_SIZE];
    char recvpacket[PACKET_SIZE];
    
    struct timeval tvsend;
    struct timeval tvrecv;
    
    int n;
    pid_t pid;
    int maxfds = 0;
    fd_set readfds;
    
    // 设定Ip信息
    bzero(&addr,sizeof(addr));
    addr.sin_family = AF_INET;
    addr.sin_addr.s_addr = inet_addr(ips);  

    // 取得socket
    sockfd = socket(AF_INET, SOCK_RAW, IPPROTO_ICMP);
    if (sockfd < 0) 
    {
        debug(LOG_ERR, "ip:%s,socket error",ips);
        return ERROR;
    }
    // 设定TimeOut时间
    timeo.tv_sec = timeout / 1000;
    timeo.tv_usec = timeout % 1000;
    
    if (setsockopt(sockfd, SOL_SOCKET, SO_SNDTIMEO, &timeo, sizeof(timeo)) == -1)
    {
        debug(LOG_ERR, "ip:%s,setsockopt error",ips);
        return ERROR;
    }
    // 设定Ping包
    memset(sendpacket, 0, sizeof(sendpacket));
    
    // 取得PID，作为Ping的Sequence ID
    pid=getpid();
    int i,packsize;
    icmp=(struct icmp*)sendpacket;
    icmp->icmp_type=ICMP_ECHO;
    icmp->icmp_code=0;
    icmp->icmp_cksum=0;
    icmp->icmp_seq=0;
    icmp->icmp_id=pid;
    packsize=8+56;
    tval= (struct timeval *)icmp->icmp_data;
    gettimeofday(tval,NULL);
    icmp->icmp_cksum=cal_chksum((unsigned short *)icmp,packsize);
    // 发包
    gettimeofday(&tvsend,NULL);
    n = sendto(sockfd, (char *)&sendpacket, packsize, 0, (struct sockaddr *)&addr, sizeof(addr));
    if (n < 1)
    {
       debug(LOG_ERR, "ip:%s,sendto error",ips);
        return ERROR;
    }
    // 接受
    // 由于可能接受到其他Ping的应答消息，所以这里要用循环
    while(1)
    {
        // 设定TimeOut时间，这次才是真正起作用的
        FD_ZERO(&readfds);
        FD_SET(sockfd, &readfds);
        maxfds = sockfd + 1;
        n = select(maxfds, &readfds, NULL, NULL, &timeo);
        if (n <= 0)
        {
            debug(LOG_ERR, "ip:%s,Time out !",ips);
            close(sockfd);
            gettimeofday(&tvrecv,NULL);
            tv_sub(&tvrecv, &tvsend);
            *ping_ms = tvrecv.tv_sec*1000 + tvrecv.tv_usec/1000;
            return ERROR;
        }
         
        // 接受
        memset(recvpacket, 0, sizeof(recvpacket));
        int fromlen = sizeof(from);
        n = recvfrom(sockfd, recvpacket, sizeof(recvpacket), 0, (struct sockaddr *)&from, &fromlen);
        if (n < 1) {
            break;
        }
        
        // 判断是否是自己Ping的回复
        char *from_ip = (char *)inet_ntoa(from.sin_addr);
        debug(LOG_DEBUG, "from ip:%s",from_ip);
         if (strcmp(from_ip,ips) != 0)
         {
            debug(LOG_DEBUG, "ip:%s,Ip wang",ips);
            gettimeofday(&tvrecv,NULL);
            tv_sub(&tvrecv, &tvsend);
            *ping_ms = tvrecv.tv_sec*1000 + tvrecv.tv_usec/1000;
            break;
         }
        
        iph = (struct ip *)recvpacket;
    
        icmp=(struct icmp *)(recvpacket + (iph->ip_hl<<2));

        debug(LOG_DEBUG, "ip:%s,icmp->icmp_type:%d,icmp->icmp_id:%d",ips,icmp->icmp_type,icmp->icmp_id);
       // 判断Ping回复包的状态
        if (icmp->icmp_type == ICMP_ECHOREPLY && icmp->icmp_id == pid)
        {
            // 正常就退出循环
            gettimeofday(&tvrecv,NULL);
            tv_sub(&tvrecv, &tvsend);
            *ping_ms = tvrecv.tv_sec*1000 + tvrecv.tv_usec/1000;
            break;
        } 
        else
        {
            // 否则继续等
            continue;
        }
    }
    
    // 关闭socket
    close(sockfd);

    debug(LOG_DEBUG, "ip:%s,Success",ips);
    return SUCCESS;
}

/******* end *******/

/*********get pid block**********/
#define MAX_LENGTH 255
int GetPidByNameEx(int *pid, int size, const char *name, int equal)
{
    DIR *d;
    FILE *fp;
    int pos = 0;
    struct dirent *de;
    char proc_path[MAX_LENGTH];
    char cmdline[MAX_LENGTH], cmdpath[MAX_LENGTH];

    sprintf(proc_path, "%s", "/proc");
    if (!(d = opendir(proc_path))) {
        return 0;
    }

    while ((de = readdir(d))) {
        int t_pid;
        int t_pri;

        if(pos >= size)
            break;

        if (de->d_name[0] == '.')
            continue;
        t_pid = atoi(de->d_name);

        if (!t_pid) {
            continue;
        }

        sprintf(cmdpath, "/proc/%s/cmdline", de->d_name);
        fp = fopen(cmdpath, "r");
        if(fp == NULL){
            continue;
        }
        
        fgets(cmdline, MAX_LENGTH, fp);
        fclose(fp);
        if(equal == 0){
            if(strcmp(cmdline, name) == 0){
                pid[pos++] = t_pid;
            }
        }else{
            if(strstr(cmdline, name)){
                pid[pos++] = t_pid;
            }
        }
    }
    closedir(d);
    
    return pos;
}

static int pid_size;
static char pid_name[MAX_LENGTH];
static int pid_value[MAX_LENGTH];

int GetPidByName(const char *name)
{
    strcpy(pid_name, name);
    pid_size = GetPidByNameEx(pid_value, MAX_LENGTH, pid_name, 1);
    if(pid_size > 0)
        return pid_value[0];
    return 0;
}

int GetPidNextByName(const char *name)
{
    static int n = 0;
    if(n >= pid_size)
        return 0;
    if(strcmp(name, pid_name) == 0)
        return pid_value[++n];

    return 0;
}
/*********get pid block end!!**********/

/******debug block********/
extern int debug_lvl;

void
_debug(char *filename, int line, int level, char *format, ...)
{
    char buf[28];
    va_list vlist;
    time_t ts;

    time(&ts);

    if (debug_lvl >= level) {

        if (level <= LOG_WARNING) {
            fprintf(stderr, "[%d][%.24s][%u](%s:%d) ", level, ctime_r(&ts, buf), getpid(),
			    filename, line);
            va_start(vlist, format);
            vfprintf(stderr, format, vlist);
            va_end(vlist);
            fputc('\n', stderr);
        } else {
            fprintf(stdout, "[%d][%.24s][%u](%s:%d) ", level, ctime_r(&ts, buf), getpid(),
			    filename, line);
            va_start(vlist, format);
            vfprintf(stdout, format, vlist);
            va_end(vlist);
            fputc('\n', stdout);
            fflush(stdout);
        }

    }
}

/***********debug block end*************/

