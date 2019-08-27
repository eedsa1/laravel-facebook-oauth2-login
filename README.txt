-para poder utilizar o login com facebook devemos baixar o facebook php-graph-sdk: https://github.com/facebook/php-graph-sdk.

-após baixar o "pacote" php-graph-sdk devemos criar nosso aplicativo na página do facebook for developers: https://developers.facebook.com.

-com o aplicativo criado devemos acessar suas configurações para recuperar o "id do aplicativo" e a "chave secreta" deste. Com essas informações devemos agora setar o endereço onde o aplicativo rodará (campo domínios do aplicativo). Por exemplo: localhost. Devemos também setar um ícone para o aplicativo. Devemos definir a categoria do aplicativo.

OBS: a url de política de privacidade e a url dos termos de serviço só precisam ser preenchidas quando o sistema for para produção. Para tornar público devemos acessar "revisão do aplicativo".

-existe um item no código chamado $permissions, para gerenciar as permissões devemos acessar o item de menu "revisão do aplicativo" no facebook developers. Por padrão são apresentadas apenas algumas opções porém podemos "adicionar itens" (está como "iniciar um envio") para outros dados do perfil do facebook.

-Caso a aplicação acuse erro de que a url não encontra-se na lista de permitidas devemos nas configurações do app (item produtos) adicionar nossa URL (com https) no "URIs de redirecionamento do OAuth válidos".