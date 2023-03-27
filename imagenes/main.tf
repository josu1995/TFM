terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.16"
    }
  }
  required_version = ">= 1.2.0"
}

provider "aws" {
  region = "us-east-1"
}

resource "aws_db_instance" "idioGrabber" {
  identifier              = "idioGrabber"
  engine                  = "mysql"
  engine_version          = "5.7.40"
  instance_class          = "db.t3.micro"
  db_name                 = "idioGrabber"
  username                = "admIdio"
  password                = "IdioGrabber5674"
  backup_window           = "03:00-04:30"
  maintenance_window      = "Mon:05:00-Mon:07:30"
  storage_type            = "gp2"
  allocated_storage       = 15
  backup_retention_period = 15
  storage_encrypted       = true
}
