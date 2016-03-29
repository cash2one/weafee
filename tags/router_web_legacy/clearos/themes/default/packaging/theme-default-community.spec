Name: theme-default-community
Group: Applications/Themes
Version: 6.5.8
Release: 1%{dist}
Summary: ClearOS Community 6 theme
License: Copyright 2011-2012 ClearFoundation
Packager: ClearFoundation
Vendor: ClearFoundation
Source: %{name}-%{version}.tar.gz
Requires: theme-default >= %{version}
Provides: theme-default-driver
Provides: system-theme
Obsoletes: app-theme-clearos5x
Buildarch: noarch

%description
ClearOS Community 6 webconfig theme

%prep
%setup -q
%build

%install
mkdir -p -m 755 $RPM_BUILD_ROOT/usr/clearos/themes/default
cp -r * $RPM_BUILD_ROOT/usr/clearos/themes/default

%files
%defattr(-,root,root)
%dir /usr/clearos/themes/default
/usr/clearos/themes/default
