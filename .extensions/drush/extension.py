"""Drush Extension

Downloads, installs and configures Drush
"""
import os
import os.path
import logging
from build_pack_utils import utils


_log = logging.getLogger('drush')


DEFAULTS = utils.FormattedDict({
    'DRUSH_VERSION': '8.1.15',
    'DRUSH_HASH': '71abcb3567856a0e522e014fd5c099ab-2',
    'DRUSH_PACKAGE': 'drush-{DRUSH_VERSION}.tar.gz',
    'DRUSH_URL': 'https://s3.eu-central-1.amazonaws.com/pcf-binary/drush/{DRUSH_PACKAGE}'
})


# Extension Methods
def preprocess_commands(ctx):
    commands = [
        [ 'echo "PATH variable..."'],
        [ 'echo "export PATH=$PATH:/home/vcap/app/php/bin:/home/vcap/app/drush" > /home/vcap/.bash_aliases']
    ]
    return commands


def service_commands(ctx):
    return {}


def service_environment(ctx):
    return {}

def compile(install):
    print 'Installing Drush %s' % DEFAULTS['DRUSH_VERSION']
    ctx = install.builder._ctx
    inst = install._installer
    workDir = os.path.join(ctx['TMPDIR'], 'drush')
    inst.install_binary_direct(
        DEFAULTS['DRUSH_URL'],
        DEFAULTS['DRUSH_HASH'],
        workDir,
        fileName=DEFAULTS['DRUSH_PACKAGE'],
        strip=True)
    (install.builder
        .move()
        .everything()
        .under('{BUILD_DIR}/drush')
        .into(workDir)
        .done())
    (install.builder
        .move()
        .everything()
        .under(workDir)
        .into('{BUILD_DIR}/drush')
        .done())
    return 0
