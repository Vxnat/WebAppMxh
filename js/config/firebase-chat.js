// Import the functions you need from the SDKs you need
import { initializeApp } from 'https://www.gstatic.com/firebasejs/11.3.0/firebase-app.js';
import { getDatabase } from 'https://www.gstatic.com/firebasejs/11.3.0/firebase-database.js';
import {
  getFirestore,
  collection,
  doc,
  query,
  where,
  limit,
  orderBy,
  getDocs,
  addDoc,
  deleteDoc,
} from 'https://www.gstatic.com/firebasejs/11.3.0/firebase-firestore.js';
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: 'AIzaSyB2DEyP-JB2lfpecgKO_cZzuYPs9jvqkwY',
  authDomain: 'webappmxh.firebaseapp.com',
  projectId: 'webappmxh',
  storageBucket: 'webappmxh.appspot.com',
  messagingSenderId: '386332823000',
  appId: '1:386332823000:web:6a2b16f1a991333f85b6ad',
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const database = getDatabase(app);
const db = getFirestore(app);

// Gửi tin nhắn
async function sendMessage(data, isGroupChat) {
  try {
    const messageRef = collection(db, !isGroupChat ? 'messages' : 'group_messages', data.conversationId, 'chat');

    await addDoc(messageRef, {
      senderId: data.senderId,
      senderAvatar: data.senderAvatar,
      text: data.message,
      timestamp: Date.now(),
    });

    console.log('Tin nhắn đã gửi thành công!');
  } catch (error) {
    console.error('Lỗi khi gửi tin nhắn:', error);
  }
}

// Xóa tin nhắn
async function deleteMessage(messageId, conversationId, isGroupChat) {
  try {
    const messageRef = doc(db, !isGroupChat ? 'messages' : 'group_messages', conversationId, 'chat', messageId);
    await deleteDoc(messageRef);
    console.log('Tin nhắn đã xóa thành công!');
  } catch (error) {
    console.error('Lỗi khi xóa tin nhắn:', error);
  }
}

// Lấy tin nhắn cuối cùng
async function getLastMessage(conversationId, isGroupChat) {
  try {
    const messageRef = collection(db, !isGroupChat ? 'messages' : 'group_messages', conversationId, 'chat');
    const q = query(messageRef, orderBy('timestamp', 'desc'), limit(1));
    const querySnapshot = await getDocs(q);
    if (!querySnapshot.empty) {
      const lastMessage = querySnapshot.docs[0];
      return { id: lastMessage.id, ...lastMessage.data() };
    }
    return null;
  } catch (error) {
    console.error('Lỗi khi lấy tin nhắn cuối cùng:', error);
    return null;
  }
}

export { sendMessage, deleteMessage, getLastMessage, db };
