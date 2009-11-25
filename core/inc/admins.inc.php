<?php
	

if(!empty($_GET['action'])){
    include('admins.inc/'.$_GET['action'].'.inc.php');
} else {

    ?>
    <h1>Usuários</h1>
    <p>
        Nesta página você pode gerenciar todos os usuários que administram o site.
    </p>
    <p>
        Selecione abaixo o que você deseja fazer:
    </p>
    <div class="action_options">
        <ul style="list-style: none;">
        <li>
        <a href="adm_main.php?section=admins&action=form&fm=criar" style="text-decoration: none;"><img src="img/layoutv1/adicionar.jpg" border="0" /></a>
        <a href="adm_main.php?section=admins&action=form&fm=criar">Cadastrar um novo usuário</a>
        </li>
        <li>
        <a href="adm_main.php?section=admins&action=form&fm=editar" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" border="0" /></a>
        <a href="adm_main.php?section=admins&action=form&fm=editar">Editar suas próprias informações</a>
        </li>
        <li>
        <a href="adm_main.php?section=admins&action=listar" style="text-decoration: none;"><img src="img/layoutv1/list.jpg" border="0" /></a>
        <a href="adm_main.php?section=admins&action=listar">Listar e editar os usuários cadastrados</a>
        </li>
        </ul>

    </div>
    <?php
}
?>
	
	