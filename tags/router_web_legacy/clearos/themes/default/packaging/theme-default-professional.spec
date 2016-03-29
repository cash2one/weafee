Name: theme-default-professional
Group: Applications/Themes
Version: 6.5.8
Release: 1%{dist}
Summary: ClearOS Professional 6 theme
License: Copyright 2011-2012 ClearCenter
Packager: ClearCenter
Vendor: ClearCenter
Source: %{name}-%{version}.tar.gz
Requires: theme-default >= %{version}
Requires: clearos-logos-professional
Requires: plymouth-scripts
Provides: theme-default-driver
Provides: system-theme
Obsoletes: theme-default-community
Buildarch: noarch

%description
ClearOS Professional 6 webconfig theme

%prep
%setup -q
%build

%install
mkdir -p -m 755 $RPM_BUILD_ROOT/usr/clearos/themes/default
cp -r * $RPM_BUILD_ROOT/usr/clearos/themes/default

%post
/usr/sbin/plymouth-set-default-theme --rebuild-initrd rings
exit 0

%files
%defattr(-,root,root)
%dir /usr/clearos/themes/default
/usr/clearos/themes/default
