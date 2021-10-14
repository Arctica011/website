#!/bin/bash
ENV_TYPE=$(sudo awk -F "=" '/APP_ENV/ {print $2}' /opt/elasticbeanstalk/deployment/env)
INSTANCE_ID=$(/opt/aws/bin/ec2-metadata -i | awk '{print $2}')
ENVIRONMENT_NAME=$(aws ec2 describe-tags \
  --output text \
  --filters "Name=resource-id,Values=$(/opt/aws/bin/ec2-metadata -i | awk '{print $2}')" \
            "Name=key,Values=elasticbeanstalk:environment-name" \
  --region "$(/opt/aws/bin/ec2-metadata -z | awk '{print substr($2, 0, length($2)-1)}')" \
  --query "Tags[*].Value")
VERSION=$(cat version.txt)


sudo -u webapp aws s3 sync s3://arctica-eb-deploy/$ENV_TYPE/ ./
sudo cat /opt/elasticbeanstalk/deployment/env | sudo tee -a .env > /dev/null
echo "VERSION=$VERSION" | sudo tee -a .env > /dev/null
echo "CLOUDWATCH_LOG_GROUP=/$ENVIRONMENT_NAME/$VERSION" | sudo tee -a .env > /dev/null
echo "CLOUDWATCH_STREAM=$INSTANCE_ID" | sudo tee -a .env > /dev/null

sudo chmod +r .env
