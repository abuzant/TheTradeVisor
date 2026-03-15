# Windsurf Resource Limits - Quick Reference

**Last Updated**: November 21, 2025  
**Status**: ACTIVE  
**Applies To**: All user processes (including Windsurf IDE)

---

## Overview

Resource limits have been implemented to prevent Windsurf IDE processes from consuming excessive system resources and causing server hangs.

**Incident that triggered this**: [Server Hang - Nov 21, 2025](/www/docs/incidents/2025-11-21_server_hang_windsurf_memory.md)

---

## Implemented Limits

### 1. Systemd Cgroup Limits (Primary Protection)

**File**: `/etc/systemd/system/user@.service.d/resource-limits.conf`

| Resource | Limit | Description |
|----------|-------|-------------|
| **MemoryMax** | 3.0GB | Hard limit - process killed if exceeded |
| **MemoryHigh** | 2.5GB | Soft limit - process throttled if exceeded |
| **CPUQuota** | 35% | Maximum 0.35 cores |
| **TasksMax** | 200 | Maximum number of processes/threads |

**Scope**: All processes running under user slice (UID 1001 - tradeadmin)

### 2. Monitoring Script

**File**: `/www/scripts/monitor_windsurf_resources.sh`

**Function**: 
- Monitors Windsurf processes every 5 minutes
- Kills processes exceeding 2.5GB memory or 150% CPU
- Aggregates total Windsurf memory footprint
- Sends email alerts on violations
- Logs all actions to `/var/log/thetradevisor/windsurf_monitor.log`

**Cron Schedule**: Every 5 minutes via `/etc/cron.d/windsurf_monitor`

---

## How It Works

### Memory Protection

1. **Normal Operation** (< 3.4GB):
   - Processes run normally
   - No throttling or intervention

2. **Soft Limit Exceeded** (3.4GB - 3.8GB):
   - Kernel throttles the process
   - Swap usage may increase
   - Performance degrades gracefully

3. **Hard Limit Exceeded** (> 3.8GB):
   - Kernel kills the process (OOM)
   - systemd logs the event
   - Process must restart

### CPU Protection

- Windsurf processes limited to 50% CPU time
- On 2-core system = maximum 0.5 cores
- Prevents CPU starvation of production services

### Process Protection

- Maximum 200 tasks (processes + threads)
- Prevents fork bombs
- Protects system stability

---

## Monitoring & Verification

### Check Current Resource Usage

```bash
# View all user slice resources
systemd-cgtop

# Check specific user limits
systemctl show user@1001.service | grep -E "Memory|CPU|Tasks"

# Monitor Windsurf processes
ps aux | grep windsurf | grep -v grep

# Check memory usage
free -h
```

### View Applied Limits

```bash
# Show memory limits (in bytes)
systemctl show user@1001.service -p MemoryMax -p MemoryHigh

# Show CPU quota
systemctl show user@1001.service -p CPUQuotaPerSecUSec

# Show task limit
systemctl show user@1001.service -p TasksMax
```

### Check Monitoring Logs

```bash
# View monitoring script output
tail -f /var/log/thetradevisor/windsurf_monitor.log

# Check for killed processes
journalctl -u user@1001.service | grep -i "killed\|oom"
```

---

## Current Status

**As of November 27, 2025 13:05 UTC**:

- ✅ Systemd limits: ACTIVE (3.0G/2.5G, 35% CPU)
- ✅ Monitoring script: ACTIVE (cron enabled)
- ✅ Current Windsurf usage: 1.4GB (18% of system memory)
- ✅ Processes: 9 Windsurf-related processes
- ✅ Status: HEALTHY

---

## Troubleshooting

### Windsurf Processes Killed Unexpectedly

**Symptom**: Windsurf disconnects or stops responding

**Check**:
```bash
# Check if process was OOM killed
journalctl -u user@1001.service | tail -50 | grep -i oom

# Check monitoring log
tail -50 /var/log/thetradevisor/windsurf_monitor.log
```

**Solution**:
- Restart Windsurf connection
- Close unused editor tabs
- Reduce number of open files
- Consider working on smaller codebase sections

### Memory Limit Too Restrictive

**Symptom**: Windsurf frequently killed, can't complete operations

**Check Current Usage**:
```bash
ps aux --sort=-%mem | grep windsurf | head -10
```

**Adjust Limits** (if needed):
```bash
# Edit the config file
sudo nano /etc/systemd/system/user@.service.d/resource-limits.conf

# Change MemoryMax and MemoryHigh values
# Example: Increase to 4.5GB
MemoryMax=4.5G
MemoryHigh=4.0G

# Reload systemd
sudo systemctl daemon-reload

# Restart user slice (WARNING: kills all user processes)
sudo systemctl restart user@1001.service
```

### Monitoring Script Not Running

**Check Cron**:
```bash
crontab -l | grep windsurf
```

**Add to Cron** (if missing):
```bash
crontab -e
# Add this line:
*/5 * * * * /www/scripts/monitor_windsurf_resources.sh >> /var/log/thetradevisor/windsurf_monitor.log 2>&1
```

---

## Maintenance

### Adjusting Limits

**When to adjust**:
- Frequent OOM kills without actual memory leaks
- System has more RAM after upgrade
- Workload patterns change

**How to adjust**:
1. Edit `/etc/systemd/system/user@.service.d/resource-limits.conf`
2. Run `sudo systemctl daemon-reload`
3. Changes apply to new processes immediately
4. Existing processes need restart to apply new limits

### Monitoring Best Practices

1. **Weekly Review**:
   - Check monitoring logs for patterns
   - Review killed processes
   - Adjust limits if needed

2. **After System Changes**:
   - RAM upgrade → Increase limits proportionally
   - CPU upgrade → Adjust CPUQuota
   - New services → Reduce user slice allocation

3. **Performance Tuning**:
   - Monitor swap usage
   - Check for memory pressure events
   - Balance between protection and usability

---

## Emergency Procedures

### Disable Limits Temporarily

```bash
# Remove the override file
sudo rm /etc/systemd/system/user@.service.d/resource-limits.conf

# Reload systemd
sudo systemctl daemon-reload

# Restart user slice
sudo systemctl restart user@1001.service
```

**WARNING**: Only do this if:
- Debugging a critical issue
- Limits are causing production problems
- You're actively monitoring the system

**Remember to re-enable** after troubleshooting!

### Kill All Windsurf Processes

```bash
# Find all Windsurf processes
ps aux | grep windsurf | grep -v grep

# Kill them all
pkill -9 -f windsurf

# Or more targeted
pkill -9 -f language_server
```

---

## Related Documentation

- [Incident Report: Server Hang Nov 21, 2025](/www/docs/incidents/2025-11-21_server_hang_windsurf_memory.md)
- [System Monitoring Guide](/www/docs/operations/system_monitoring.md)
- [Resource Management Best Practices](/www/docs/operations/resource_management.md)

---

## Technical References

- [systemd Resource Control](https://www.freedesktop.org/software/systemd/man/systemd.resource-control.html)
- [Linux Memory Management](https://www.kernel.org/doc/html/latest/admin-guide/mm/index.html)
- [cgroups v2 Documentation](https://www.kernel.org/doc/html/latest/admin-guide/cgroup-v2.html)

---

## Change Log

| Date | Change | Reason |
|------|--------|--------|
| 2025-11-21 | Initial implementation | Server hang incident |
| 2025-11-21 | Added monitoring script | Proactive protection |

---

**Maintained By**: DevOps Team  
**Contact**: ruslan@abuzant.com  
**Emergency Contact**: hello@thetradevisor.com
