#!/bin/bash
set -e

HOME_DIR=${PWD}/..
BUILD_DIR=${HOME_DIR}/CodeIgniter

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
pushd CodeIgniter/assets

# Bootstrap
git clone https://github.com/twbs/bootstrap.git

# Font Awesome
git clone https://github.com/FortAwesome/Font-Awesome.git

popd


# CLEANUP
rm -rf .tmp CodeIgniter-AddOn
