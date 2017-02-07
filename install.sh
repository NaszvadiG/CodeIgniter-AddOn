#!/bin/bash
set -e

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
BUILD_DIR=../${DIR}/CodeIgniter

# PRE
rm -rf ${BUILD_DIR}
rm -rf ../.tmp
mkdir ../.tmp


#######################
# CodeIgniter 
#######################
pushd ../

# CodeIgniter
git clone https://github.com/bcit-ci/CodeIgniter.git

# CodeIgniter-AddOn
rsync -ac CodeIgniter-AddOn/application CodeIgniter
rsync -ac CodeIgniter-AddOn/assets CodeIgniter

# PHPExcel : .tmp
pushd .tmp
git clone https://github.com/PHPOffice/PHPExcel.git
pushd PHPExcel
rsync -ac Classes/* ${BUILD_DIR}/application/libraries
popd
popd


#######################
# assets 
#######################
pushd assets

# Bootstrap
git clone https://github.com/twbs/bootstrap.git

# Font Awesome
git clone https://github.com/FortAwesome/Font-Awesome.git

popd


# CLEANUP
rm -rf .tmp
