<div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header pt-1 pb-1 ps-2 pe-2">

        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          
      </div>

 <style>.modal-content { height: calc(100vh - 3rem); width: calc(80vw - 3rem); display: flex; flex-direction: column;  }</style>

<div class="modal-body pt-2 pb-2 d-flex flex-column" style="overflow: auto;">

        <?php echo $content?>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>

