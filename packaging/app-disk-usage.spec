
Name: app-disk-usage
Epoch: 1
Version: 2.1.15
Release: 1%{dist}
Summary: Disk Usage Report
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
The Disk Usage Report app displays your system hard disk usage and provides a clickable drill down report.

%package core
Summary: Disk Usage Report - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: duc >= 1.3.3

%description core
The Disk Usage Report app displays your system hard disk usage and provides a clickable drill down report.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/disk_usage
cp -r * %{buildroot}/usr/clearos/apps/disk_usage/

install -d -m 755 %{buildroot}/var/clearos/disk_usage
install -d -m 0755 %{buildroot}/var/clearos/disk_usage/backup
install -D -m 0644 packaging/app-disk-usage.cron %{buildroot}/etc/cron.d/app-disk-usage
install -D -m 0755 packaging/duc-updatedb %{buildroot}/usr/sbin/duc-updatedb

%post
logger -p local6.notice -t installer 'app-disk-usage - installing'

%post core
logger -p local6.notice -t installer 'app-disk-usage-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/disk_usage/deploy/install ] && /usr/clearos/apps/disk_usage/deploy/install
fi

[ -x /usr/clearos/apps/disk_usage/deploy/upgrade ] && /usr/clearos/apps/disk_usage/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-disk-usage - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-disk-usage-core - uninstalling'
    [ -x /usr/clearos/apps/disk_usage/deploy/uninstall ] && /usr/clearos/apps/disk_usage/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/disk_usage/controllers
/usr/clearos/apps/disk_usage/htdocs
/usr/clearos/apps/disk_usage/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/disk_usage/packaging
%dir /usr/clearos/apps/disk_usage
%dir %attr(755,webconfig,webconfig) /var/clearos/disk_usage
%dir /var/clearos/disk_usage/backup
/usr/clearos/apps/disk_usage/deploy
/usr/clearos/apps/disk_usage/language
/usr/clearos/apps/disk_usage/libraries
/etc/cron.d/app-disk-usage
/usr/sbin/duc-updatedb
