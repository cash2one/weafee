Name: theme-default
Group: Applications/Themes
Version: 6.5.8
Release: 1%{dist}
Summary: ClearOS 6 base theme
License: Copyright 2011-2013 ClearFoundation
Packager: ClearFoundation
Vendor: ClearFoundation
Source: %{name}-%{version}.tar.gz
Requires: clearos-framework >= 6.5.0
Requires: theme-default-driver
Buildarch: noarch

%description
ClearOS 6 base webconfig theme

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
