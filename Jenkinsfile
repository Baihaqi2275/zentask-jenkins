pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'php -v'
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
            }
        }

        stage('Generate Env') {
            steps {
                sh 'cp .env.example .env || true'
                sh 'php artisan key:generate || true'
            }
        }

        stage('Build') {
            steps {
                echo 'Build success'
            }
        }
    }
}
