<?php /* Smarty version 2.6.26, created on 2013-04-09 21:41:13
         compiled from admin_initialise.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin_header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
<div>
  <table cellpadding="0" cellspacing="0" border="0" width="100%" class="adminform">
      <tr>
        <td align="left">
        
          <h2><?php echo @_HWDVIDS_TITLE_FINSET; ?>
</h2>

          <input type="checkbox" name="cats" value="1" checked="checked"><?php echo @_HWDVIDS_INS_SAMP_CATS; ?>
<br />
          <input type="checkbox" name="youtube" value="1" checked="checked"><?php echo @_HWDVIDS_INS_YT; ?>
<br />
          <input type="checkbox" name="google" value="1" checked="checked"><?php echo @_HWDVIDS_INS_GV; ?>
<br /><br />
          
          <p><?php echo @_HWDVIDS_JW_LIC; ?>
</p>
          
          <select name="jwflv_license">
            <option value="0"><?php echo @_HWDVIDS_JW_AGREE; ?>
</option>
            <option value="1"><?php echo @_HWDVIDS_JW_DECLINE; ?>
</option>
            <option value="2"><?php echo @_HWDVIDS_JW_EXISTING; ?>
</option>
            <option value="3"><?php echo @_HWDVIDS_JW_SKIP; ?>
</option>
          </select>
          <br /><br />
          
          <input type="submit" value="<?php echo @_HWDVIDS_BUTTON_FINSET; ?>
">
        
        </td>
      </tr>
  </table>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'admin_footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
