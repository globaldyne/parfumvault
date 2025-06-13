<?php if (!defined('pvault_panel')){ die('Not Found');} ?>
<!-- Chatbot -->
<div id="chatbot">
  <div id="chatbot-icon"><i class="fa fa-robot"></i></div>
</div>

<div id="chatbot-modal">
    <div id="chatbot-modal-header" class="d-flex align-items-center justify-content-between">
      <span>
        <i class="fa fa-robot me-2"></i>
        Chat with Perfumers AI (BETA)
      </span>
      <span style="cursor: pointer;" id="chatbot-close">&times;</span>
    </div>
    <div id="chatbot-modal-body">
      <?php 
      $fullNameParts = explode(' ', $user['fullName']);
      $firstName = $fullNameParts[0];
      ?>
      <p>
        Hey <?php echo htmlspecialchars($firstName) ?>, I'm <strong>Perfumers AI</strong>, your personal assistant.<br>
        <span class="fw-bold text-info mt-2 pt-2 d-block">What can I help you with?</span>
        <ul class="list-group list-group-flush my-2">
          <li class="list-group-item"><i class="fas fa-leaf text-success me-2"></i>Find information about ingredients</li>
          <li class="list-group-item"><i class="fas fa-vial text-warning me-2"></i>Generate base formulas</li>
          <li class="list-group-item"><i class="fas fa-sync-alt text-primary me-2"></i>Suggest ingredient replacements</li>
          <li class="list-group-item"><i class="fas fa-question-circle text-info me-2"></i>Answer perfumery-related questions</li>
          <li class="list-group-item"><i class="fas fa-magic text-secondary me-2"></i>And more!</li>
        </ul>
        <span class="text-muted">How can I assist you today?</span>
      </p>
    </div>
    <div id="chatbot-modal-footer" class="d-flex align-items-center">
        <input type="text" id="chatbot-input" class="form-control form-control-lg me-2 flex-grow-1" placeholder="Ask Perfumers AI..." aria-label="Chat input" />
        <button id="chatbot-send" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center p-0" type="button" title="Send" style="width: 48px; height: 48px;">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
  </div>
</div>