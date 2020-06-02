pipeline {
	agent {
    docker {
      image 'node:12.13.1-alpine'
      args '-u root'
      reuseNode true
    }
  }
  environment {
    APP_NAME = "square-one"
    SLACK_CHANNEL = "squareone"
    GIT_SSH_KEYS = "${env.APP_NAME}-ssh-key"
  }
  parameters {
    choice(name: 'DEPLOY_ENVIRONMENT',
      choices: 'null\ndev\nstaging\nproduction',
      description: 'To which environment should be deployed ?'
    )
  }

  stages {
    stage('Slack message') {
      steps {
        script {
          env.MSG_SLACK   = "${params.DEPLOY_ENVIRONMENT == "null" ? " " : "to `${params.DEPLOY_ENVIRONMENT}` "}"
        }
        // Debug
        echo "${params.DEPLOY_ENVIRONMENT} - ${env.MSG_SLACK}"

        slackSend(
          channel: "${SLACK_CHANNEL}",
          message: "`${APP_NAME}` deploy of branch `${env.BRANCH_NAME}` started: (build: <${RUN_DISPLAY_URL}|#${BUILD_NUMBER}>)"
        )
      }
    }
    stage('Checkout code Github') {
      steps {
        checkout scm
      }
    }
    stage('Bootstrap') {
      steps {
        sh script: "./script/bootstrap", label: "Running Bootstrap"
      }
    }

    stage('Build Processes') {
      parallel {
        stage('Composer') {
          steps {
            withCredentials([
							file(credentialsId: "square-one-compose-plugins-keys", variable: "ENV_FILE"),
							string(credentialsId: "tr1b0t-github-api-token", variable: "GIT_TOKEN")
							]) {
                sh script: "cp ${ENV_FILE} .env", label: "Copy Composer .env to the root folder"
								sh script: "composer config -g github-oauth.github.com ${GIT_TOKEN}", label: "Set composer token"
                sh script: "./script/cibuild composer", label: "Running CI Build composer"
            }
          }
        }

        stage('Node') {
          steps {
            sh script: "./script/cibuild node", label: "Running CI Build"
           }
        }
      }
    }

    stage('Deploy to Dev') {
    	when {
        anyOf {
          expression { env.BRANCH_NAME == 'develop' && params.DEPLOY_ENVIRONMENT == 'null'}
          expression { params.DEPLOY_ENVIRONMENT == 'dev' }
        }
      }
      steps {
        sshagent (credentials: ["${GIT_SSH_KEYS}"]) {
          sh script: "./script/cideploy dev", label: 'Deploy to Dev'
        }
      }
    }

    stage('Deploy to Staging') {
    	when {
        anyOf {
          expression { env.BRANCH_NAME == 'server/staging' && params.DEPLOY_ENVIRONMENT == 'null'}
          expression { params.DEPLOY_ENVIRONMENT == 'staging' }
        }
      }
      steps {
        sshagent (credentials: ["${GIT_SSH_KEYS}"]) {
          sh script: './script/cideploy staging', label: 'Deploy to Staging'
        }
      }
    }

    stage('Deploy to Production') {
    	when {
        anyOf {
          expression { env.BRANCH_NAME == 'server/production' && params.DEPLOY_ENVIRONMENT == 'null'}
          expression { params.DEPLOY_ENVIRONMENT == 'production' }
        }
      }
      steps {
        sshagent (credentials: ["${GIT_SSH_KEYS}"]) {
          sh script: './script/cideploy production', label: 'Deploy to Production'
        }
      }
    }
  }
  post {
    always {
      cleanWs()
    }
    failure {
      slackSend(
        channel: "${SLACK_CHANNEL}", color: 'danger',
        message: "`${APP_NAME}` deploy of branch `${env.BRANCH_NAME}` ${env.MSG_SLACK}failed: (build: <${RUN_DISPLAY_URL}|#${BUILD_NUMBER}>)"
      )
    }
    success {
      slackSend(
        channel: "${SLACK_CHANNEL}", color: 'good',
        message: "`${APP_NAME}` deploy of branch `${env.BRANCH_NAME}` ${env.MSG_SLACK}was successful: (build: <${RUN_DISPLAY_URL}|#${BUILD_NUMBER}>)"
      )
    }
  }
}