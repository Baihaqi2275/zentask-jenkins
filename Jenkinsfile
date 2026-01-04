pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install PHP and Composer Check') {
            steps {
                bat 'php -v'
                bat 'composer -V'
            }
        }

        stage('Install Dependencies') {
            steps {
                bat 'composer install --no-interaction --prefer-dist --optimize-autoloader'
            }
        }

        stage('Generate Env') {
            steps {
                bat 'if not exist .env copy .env.example .env'
                bat 'php artisan key:generate'
            }
        }

        stage('Build') {
            steps {
                echo 'Build success'
            }
        }
    }
}
