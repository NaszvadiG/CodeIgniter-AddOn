#!/bin/bash
set -e

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
BUILD_DIR=${DIR}/CodeIgniter

# PRE
rm -rf ${BUILD_DIR}

#######################
# CodeIgniter 
#######################

# CODEIGNITER
git clone https://github.com/bcit-ci/CodeIgniter.git
git clone https://github.com/zblues/CodeIgniter.git

# PHPExcel
git clone https://github.com/PHPOffice/PHPExcel.git
pushd PHPExcel
cp Classes/* ${BUILD_DIR}/application/libraries
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
