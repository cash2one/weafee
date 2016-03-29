#ifndef _UTIL_H_
#define _UTIL_H_

/** @brief Execute a shell command
 */
int execute(char *cmd_line, int quiet);
struct in_addr *wd_gethostbyname(const char *name);

/* @brief Get MAC address of an interface */
char *get_iface_mac(const char *ifname);

#define LOCK_GHBN() do { \
	debug(LOG_DEBUG, "Locking wd_gethostbyname()"); \
	pthread_mutex_lock(&ghbn_mutex); \
	debug(LOG_DEBUG, "wd_gethostbyname() locked"); \
} while (0)

#define UNLOCK_GHBN() do { \
	debug(LOG_DEBUG, "Unlocking wd_gethostbyname()"); \
	pthread_mutex_unlock(&ghbn_mutex); \
	debug(LOG_DEBUG, "wd_gethostbyname() unlocked"); \
} while (0)

/** @brief Used to output messages.
 *The messages will include the finlname and line number, and will be sent to syslog if so configured in the config file 
 */
//#define debug(level, format...) _debug(__FILE__, __LINE__, level, format)

/* robert yang*/
#define debug(level, format...)


/** @internal */
void _debug(char *filename, int line, int level, char *format, ...);

#endif /* _UTIL_H_ */

