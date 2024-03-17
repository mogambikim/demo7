
        </section>
        </div>
        <footer class="main-footer">
            <div class="pull-right" id="version" onclick="location.href = '{$_url}community#latestVersion';"></div>
            Billing Software by <a href="https://FreeIspRadius.com" rel="nofollow noreferrer noopener"
                target="_blank">FreeIspRadius</a>, Theme by <a href="https://adminlte.io/" rel="nofollow noreferrer noopener"
                target="_blank">AdminLTE</a>
        </footer>
        </div>
        <script src="ui/ui/scripts/jquery.min.js"></script>
        <script src="ui/ui/scripts/bootstrap.min.js"></script>
        <script src="ui/ui/scripts/adminlte.min.js"></script>
        <script src="ui/ui/scripts/plugins/select2.min.js"></script>
        <script src="ui/ui/scripts/custom.js"></script>

        {if isset($xfooter)}
            {$xfooter}
        {/if}
        {literal}
            <script>
                $(document).ready(function() {
                    $('.select2').select2({theme: "bootstrap"});
                });

                $(document).ready(function() {
                    $('.select2tag').select2({theme: "bootstrap", tags: true});
                });

                var listAtts = document.querySelectorAll(`[api-get-text]`);
                listAtts.forEach(function(el) {
                    $.get(el.getAttribute('api-get-text'), function(data) {
                        el.innerHTML = data;
                    });
                });
            </script>
        {/literal}
























<div class="chatbot-container" id="chatbot-container">
  <div class="chatbot-header" id="chatbot-header">
    FreeIspradius AI
    <div>
      <button class="minimize-button" id="minimize-button">&#8212;</button>
      <button class="close-button" id="close-button">&times;</button>
      <button class="expand-button" id="expand-button">&#8599;</button>
    </div>
  </div>
  <div class="chatbot-preferences">
    <input type="text" id="isp-input" placeholder="Enter your ISP">
 
    <button id="preferences-submit" onclick="updateUserPreferences()">Submit</button>
    <div class="success-message" id="preferences-success">Preferences submitted successfully!</div>
  </div>
  <div class="chatbot-feedback">
    <input type="text" id="feedback-input" placeholder="Enter your feedback">
    <button id="feedback-submit">Submit</button>
    <div class="success-message" id="feedback-success">Feedback submitted successfully!</div>
  </div>
  <div class="chatbot-messages" id="chatbot-messages">
    <!-- Messages will be dynamically added here -->
  </div>
  <div class="chatbot-input" id="chatbot-input">
    <input type="text" id="user-input" placeholder="Type your message...">
    <button id="send-button">Send</button>
  </div>
</div>

<div class="chatbot-label" id="chatbot-label">AI Assistant</div>

   {literal}

   
<script>
  // Get DOM elements
  const chatbotContainer = document.getElementById("chatbot-container");
  const chatbotHeader = document.getElementById("chatbot-header");
  const chatbotLabel = document.getElementById("chatbot-label");
  const chatbotMessages = document.getElementById("chatbot-messages");
  const chatbotInput = document.getElementById("chatbot-input");
  const userInput = document.getElementById("user-input");
  const sendButton = document.getElementById("send-button");
  const minimizeButton = document.getElementById("minimize-button");
  const closeButton = document.getElementById("close-button");
  const expandButton = document.getElementById("expand-button");

  // Flag to track if the chatbot is currently minimized
  let isChatbotMinimized = false;
  let isChatbotExpanded = false;

  // Store the conversation history
  let conversationHistory = [];

  // Store user preferences
  let userPreferences = {
    isp: "",
    billingSoftware: ""
  };

  // Store user feedback
  let userFeedback = [];

  // Function to toggle the chatbot visibility
  function toggleChatbot() {
    if (chatbotContainer.style.display === "none") {
      chatbotContainer.style.display = "block";
      chatbotInput.style.display = "flex";
    } else {
      chatbotContainer.style.display = "none";
      chatbotInput.style.display = "none";
    }
  }

  // Function to minimize the chatbot
  function minimizeChatbot() {
    if (!isChatbotMinimized) {
      chatbotContainer.style.height = "50px";
      chatbotMessages.style.display = "none";
      chatbotInput.style.display = "none";
      isChatbotMinimized = true;
    } else {
      chatbotContainer.style.height = "500px";
      chatbotMessages.style.display = "block";
      chatbotInput.style.display = "flex";
      isChatbotMinimized = false;
    }
  }

  function expandChatbot() {
    if (!isChatbotExpanded) {
      chatbotContainer.classList.add("expanded");
      isChatbotExpanded = true;
      expandButton.innerHTML = "&#8690;"; // Update expand button icon
    } else {
      chatbotContainer.classList.remove("expanded");
      isChatbotExpanded = false;
      expandButton.innerHTML = "&#8599;"; // Update expand button icon
    }
  }

  // Function to close the chatbot
  function closeChatbot() {
    chatbotContainer.style.display = "none";
    chatbotInput.style.display = "none";
  }

  // Function to add the welcome message
  function addWelcomeMessage() {
    const welcomeMessageElement = addMessage("", false);
    writeResponse(welcomeMessageElement.querySelector("span"), "Welcome to FreeIspradius AI Assistant! How can I assist you today?");
  }

  // Call the addWelcomeMessage function when the DOM is loaded
  document.addEventListener("DOMContentLoaded", addWelcomeMessage);

  function addMessage(message, isUserMessage) {
    const messageElement = document.createElement("div");
    messageElement.classList.add("chatbot-message");
    if (isUserMessage) {
      messageElement.classList.add("user-message");
    } else {
      messageElement.classList.add("bot-message");
    }
    const messageText = document.createElement("span");
    messageText.textContent = message;
    messageElement.appendChild(messageText);
    chatbotMessages.insertBefore(messageElement, chatbotMessages.firstChild);
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    return messageElement;
  }

  // Function to write the response word by word
  function writeResponse(messageText, response) {
    let index = 0;
    const interval = setInterval(() => {
      if (index < response.length) {
        messageText.textContent += response.charAt(index);
        index++;
      } else {
        clearInterval(interval);
      }
    }, 50);
  }

  // Function to update user preferences
  function updateUserPreferences() {
    const ispInput = document.getElementById("isp-input");
    const billingSoftwareInput = document.getElementById("billing-software-input");

    userPreferences.isp = ispInput.value.trim();
    userPreferences.billingSoftware = billingSoftwareInput.value.trim();
  }









function sendMessage() {
  const message = userInput.value.trim();
  if (message !== "") {
    const userMessageElement = addMessage(message, true);
    userInput.value = "";

    // Add user message to the conversation history
    conversationHistory.push({ role: "user", content: message });

    // Show thinking message with fluctuating dots
    const thinkingMessageElement = addMessage("", false);
    let dots = "";
    const thinkingInterval = setInterval(() => {
      if (chatbotMessages.contains(thinkingMessageElement)) {
        dots = dots.length < 3 ? dots + "." : "";
        thinkingMessageElement.querySelector("span").textContent = "Thinking" + dots;
      } else {
        clearInterval(thinkingInterval);
      }
    }, 500);

    // Hide chatbot preferences and feedback sections
    document.querySelector(".chatbot-preferences").classList.add("hidden");
    document.querySelector(".chatbot-feedback").classList.add("hidden");
    chatbotMessages.classList.add("expanded");

    // Send message to the chatbot server
    fetch("https://rasa.freeispradius.com/webhooks/rest/webhook", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        sender: "user",
        message: message,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        clearInterval(thinkingInterval);
        if (chatbotMessages.contains(thinkingMessageElement)) {
          chatbotMessages.removeChild(thinkingMessageElement);
        }

        const botResponseElement = addMessage("", false);

        if (data.length === 0 || data[0].text.trim() === "") {
          // If no response from the chatbot server, use OpenAI's GPT-3.5-turbo directly
          useOpenAI(botResponseElement, message, conversationHistory);
        } else {
          // If response from the chatbot server, enhance it using OpenAI
          const rasaResponse = data[0].text.trim();
          enhanceResponseWithOpenAI(botResponseElement, message, conversationHistory, rasaResponse);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        clearInterval(thinkingInterval);
        if (chatbotMessages.contains(thinkingMessageElement)) {
          chatbotMessages.removeChild(thinkingMessageElement);
        }
        const botResponseElement = addMessage("", false);
        writeResponse(botResponseElement.querySelector("span"), "Oops! Something went wrong. Please try again.");
      });
  }
}

function useOpenAI(botResponseElement, message, conversationHistory) {
  const apiKeyPart1 = "sk-we85JXVE21snX83cgvDU";
  const apiKeyPart2 = "T3BlbkFJIlQTx1mbks98Vy";
  const apiKeyPart3 = "R2ZIJC";
  const openaiApiKey = apiKeyPart1 + apiKeyPart2 + apiKeyPart3;

  const maskedApiKey = openaiApiKey.replace(/\w/g, '*').slice(0, 10) + '...';
  console.log('Masked API Key:', maskedApiKey);

  // Limit the input to the first three paragraphs
  const limitedMessage = message.split("\n\n").slice(0, 3).join("\n\n");

  fetch("https://api.openai.com/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Authorization": `Bearer ${openaiApiKey}`,
    },
    body: JSON.stringify({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: `You are an AI assistant specialized in networking, ISPs, internet providers, ISP billing software, and related topics. Limit your responses to these subjects and provide answers in about three paragraphs. The user's ISP is ${userPreferences.isp} and their billing software is ${userPreferences.billingSoftware}.`
        },
        ...conversationHistory,
        { role: "user", content: limitedMessage }
      ],
      max_tokens: 300,
      n: 1,
      temperature: 0.7,
    }),
  })
    .then((response) => response.json())
    .then((openaiData) => {
      const generatedResponse = openaiData.choices[0].message.content.trim();
      writeResponse(botResponseElement.querySelector("span"), generatedResponse);

      // Add assistant response to the conversation history
      conversationHistory.push({ role: "assistant", content: generatedResponse });
    })
    .catch((error) => {
      console.error("Error:", error);
      writeResponse(botResponseElement.querySelector("span"), "Oops! Something went wrong with the Remote Isp API. Please try again.");
    });
}

function enhanceResponseWithOpenAI(botResponseElement, message, conversationHistory, rasaResponse) {
  const apiKeyPart1 = "sk-we85JXVE21snX83cgvDU";
  const apiKeyPart2 = "T3BlbkFJIlQTx1mbks98Vy";
  const apiKeyPart3 = "R2ZIJC";
  const openaiApiKey = apiKeyPart1 + apiKeyPart2 + apiKeyPart3;

  const maskedApiKey = openaiApiKey.replace(/\w/g, '*').slice(0, 10) + '...';
  console.log('Masked API Key:', maskedApiKey);

  // Limit the input to the first three paragraphs
  const limitedMessage = message.split("\n\n").slice(0, 3).join("\n\n");

  fetch("https://api.openai.com/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Authorization": `Bearer ${openaiApiKey}`,
    },
    body: JSON.stringify({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: `You are an AI assistant specialized in the FreeIspRadius billing software. Your task is to enhance and provide additional information or clarification based on the context and initial response about FreeIspRadius, without repeating or contradicting it. Stay focused on FreeISPRadius and avoid deviating from the topic.`
        },
        ...conversationHistory,
        {
          role: "user",
          content: `Here is the context: ${limitedMessage}

Initial response from Rasa about FreeISPRadius: ${rasaResponse}

Please enhance the response with additional relevant information, examples, or clarifications related to FreeISPRadius. Avoid repeating or contradicting the initial response and stay focused on FreeIspRadius.go straight to enhancing dont start your sentences with enhanced version,absolutely,certainly or something similar`
        }
      ],
      max_tokens: 250,
      n: 1,
      temperature: 0.2, // Lower temperature for more focused responses
    }),
  })
    .then((response) => response.json())
    .then((openaiData) => {
      const enhancedResponse = openaiData.choices[0].message.content.trim();
      writeResponse(botResponseElement.querySelector("span"), enhancedResponse);

      // Add assistant response to the conversation history
      conversationHistory.push({ role: "assistant", content: enhancedResponse });
    })
    .catch((error) => {
      console.error("Error:", error);
      writeResponse(botResponseElement.querySelector("span"), "Oops! Something went wrong with the OpenAI API. Please try again.");
    });
}












  // Function to handle user feedback submission
  function handleFeedbackSubmission() {
    const feedbackInput = document.getElementById("feedback-input");
    const feedback = feedbackInput.value.trim();

    if (feedback !== "") {
      userFeedback.push(feedback);
      feedbackInput.value = "";
      console.log("User feedback:", userFeedback);
      // TODO: Implement logic to send feedback to a server or perform analysis
    }
  }

  // Event listener for chatbot header click
  chatbotHeader.addEventListener("click", toggleChatbot);

  // Event listener for chatbot label click
  chatbotLabel.addEventListener("click", toggleChatbot);

  // Event listener for send button click
  sendButton.addEventListener("click", sendMessage);

  // Event listener for Enter key press in the input field
  userInput.addEventListener("keyup", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      sendMessage();
    }
  });

  // Event listener for minimize button click
  minimizeButton.addEventListener("click", function (event) {
    event.stopPropagation();
    minimizeChatbot();
  });

  // Event listener for close button click
  closeButton.addEventListener("click", function (event) {
    event.stopPropagation();
    closeChatbot();
  });

  // Event listener for expand button click
  expandButton.addEventListener("click", function (event) {
    event.stopPropagation();
    expandChatbot();
  });

  // Event listener for feedback submission
  document.getElementById("feedback-submit").addEventListener("click", handleFeedbackSubmission);



  // Function to update user preferences
function updateUserPreferences() {
  const ispInput = document.getElementById("isp-input");
  const billingSoftwareInput = document.getElementById("billing-software-input");

  userPreferences.isp = ispInput.value.trim();
  userPreferences.billingSoftware = billingSoftwareInput.value.trim();

  // Display success message
  const preferencesSuccess = document.getElementById("preferences-success");
  preferencesSuccess.style.display = "block";
  setTimeout(() => {
    preferencesSuccess.style.display = "none";
  }, 2000);

  // Clear input fields
  ispInput.value = "";
  billingSoftwareInput.value = "";
}

// Function to handle user feedback submission
function handleFeedbackSubmission() {
  const feedbackInput = document.getElementById("feedback-input");
  const feedback = feedbackInput.value.trim();

  if (feedback !== "") {
    userFeedback.push(feedback);
    feedbackInput.value = "";

    // Display success message
    const feedbackSuccess = document.getElementById("feedback-success");
    feedbackSuccess.style.display = "block";
    setTimeout(() => {
      feedbackSuccess.style.display = "none";
    }, 2000);

    console.log("User feedback:", userFeedback);
    // TODO: Implement logic to send feedback to a server or perform analysis
  }
}

// Event listener for feedback submission
document.getElementById("feedback-submit").addEventListener("click", handleFeedbackSubmission);




  
</script>

{/literal}




        </body>

</html>