# Projeto de Gestão de Diagramas com Symfony, MongoDB e Docker

Este é um projeto de gerenciamento de **Projetos** e **Diagramas** construído com Symfony 7 e MongoDB. O sistema permite a criação, visualização e exclusão de projetos, que podem conter diagramas hierárquicos. O projeto usa o Doctrine MongoDB ODM para interação com o MongoDB e pode ser facilmente executado em um ambiente Docker.

## Funcionalidades

- **Criar Projeto**: Criação de projetos com título e diagramas.
- **Listar Projetos**: Exibição de todos os projetos com seus diagramas.
- **Excluir Projeto**: Exclusão de projetos com base no seu ID.
  
### Arquitetura
- **Project**: Entidade principal do projeto, que contém um título e uma coleção de diagramas.
- **Diagram**: Documento embutido dentro do projeto, representando um diagrama, que pode ter filhos, formando uma estrutura hierárquica.

## Pré-requisitos

Antes de começar, certifique-se de ter o seguinte instalado:

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Instalação

### 1. Clone o Repositório

Clone este repositório para o seu ambiente local:

```bash
git clone https://github.com/HigorZicaDev/symfony-mongodb.git
cd symfony-mongodb
```

### 2. Use o Docker Compose para configurar o ambiente

O Docker Compose será usado para orquestrar o ambiente do MongoDB e a aplicação Symfony. Com o `docker-compose.yaml` já configurado, basta rodar o comando abaixo para iniciar os containers.

### 3. Inicie os Containers com Docker Compose

Com o arquivo `docker-compose.yaml` configurado, use o seguinte comando para iniciar os containers:

```bash
docker-compose up -d --build
```

Esse comando vai:

1. Criar e iniciar o container do MongoDB.
2. Criar e iniciar o container da aplicação Symfony.

Após a execução do comando, você verá logs indicando que os containers estão sendo iniciados.

### 4. Acessar o Projeto

A aplicação estará disponível em `http://localhost:8000`. O MongoDB estará acessível dentro do container, mas se necessário, você pode configurar sua conexão de banco de dados no arquivo `.env` para outro host de sua preferência.

## Docker Compose

O arquivo `docker-compose.yaml` contém a configuração necessária para rodar o Symfony e o MongoDB no Docker. Aqui está o conteúdo do arquivo:

### `docker-compose.yaml`

```yaml
services:
  php:
    container_name: php-app-symfony
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      args:
        USER_ID: ${UID:-1000}
        GROUP_ID: ${GID:-1000}
    volumes:
      - .:/var/www:delegated
    depends_on:
      - mongodb
    networks:
      - app_network

  nginx:
    build: ./docker/nginx
    ports:
      - "8000:80"
    volumes:
      - ./public:/var/www/public:delegated
    depends_on:
      - php
    networks:
      - app_network

  mongodb:
    image: mongo:latest
    container_name: mongodb-app-symfony
    environment:
      MONGO_INITDB_DATABASE: ${database_name}
      MONGO_INITDB_ROOT_USERNAME: ${database_user}
      MONGO_INITDB_ROOT_PASSWORD: ${database_password}
    volumes:
      - mongodb_data:/data/db
    networks:
      - app_network
    ports:
      - "27017:27017"   # Expondo a porta 27017 para o host

networks:
  app_network:

volumes:
  mongodb_data:

```

### Explicação:

- **mongo**: Este serviço utiliza a imagem oficial do MongoDB. Ele mapeia a porta `27017` para o host e armazena os dados em um volume persistente chamado `mongo_data`.
- **app**: Este serviço é responsável por rodar a aplicação Symfony. Ele depende do serviço MongoDB e mapeia a porta `8000` para o host.
- **environment**: Configura a variável `MONGO_URL`, que aponta para o container MongoDB (`mongo:27017`), permitindo que o Symfony se conecte ao banco de dados MongoDB.

### 5. Executando Comandos Dentro do Container

Se você precisar rodar comandos dentro do container da aplicação Symfony, como o `composer install` ou rodar o servidor Symfony, use o seguinte comando:

```bash
docker-compose exec app bash
```

Isso abrirá um terminal dentro do container da aplicação. Agora, você pode rodar os comandos necessários, por exemplo:

```bash
# Para rodar o servidor de desenvolvimento Symfony
symfony server:start
```

Ou executar o Composer, caso tenha necessidade de instalar dependências:

```bash
composer install
```

## Endpoints da API

### 1. Criar Projeto

**Rota**: `POST /api/project/create`

**Corpo da Requisição**:

```json
{
    "title": "Projeto Exemplo",
    "diagram": [
        {
            "title": "Diagrama 1",
            "childs": [
                {
                    "title": "Diagrama Filho 1"
                }
            ]
        }
    ]
}
```

**Resposta**:

```json
{
    "message": "Project created",
    "id": "id_do_projeto"
}
```

### 2. Listar Projetos

**Rota**: `GET /api/project/list`

**Resposta**:

```json
[
    {
        "id": "id_do_projeto",
        "title": "Projeto Exemplo",
        "diagrams": [
            {
                "title": "Diagrama 1",
                "children": [
                    {
                        "title": "Diagrama Filho 1",
                        "children": []
                    }
                ]
            }
        ]
    }
]
```

### 3. Remover Projeto

**Rota**: `DELETE /api/project/remove/{id}`

**Parâmetros**: `id` - ID do projeto a ser excluído.

**Resposta**:

```json
{
    "message": "Project removed successfully"
}
```

## Estrutura de Diretórios

O projeto segue a seguinte estrutura de diretórios:

```
├── src
│   ├── Controller
│   │   └── Api
│   │       └── ProjectController.php
│   ├── Document
│   │   ├── Diagram.php
│   │   └── Project.php
├── templates
│   └── base.html.twig
├── .env
├── composer.json
├── docker-compose.yaml
├── Dockerfile
└── README.md
```

## Contribuindo

Se você deseja contribuir com o projeto, siga os seguintes passos:

1. Fork este repositório.
2. Crie uma nova branch (`git checkout -b feature/nova-funcionalidade`).
3. Faça suas alterações e commit (`git commit -am 'Adiciona nova funcionalidade'`).
4. Push para a branch (`git push origin feature/nova-funcionalidade`).
5. Abra um pull request.

## Licença

Este projeto é licenciado sob a [MIT License](LICENSE).

---