<template>
  <div class="skeleton-card">
    <div class="skeleton-image"></div>
    <div class="skeleton-content">
      <div class="skeleton-line title"></div>
      <div class="skeleton-line text"></div>
      <div class="skeleton-line text short"></div>
      <div class="skeleton-footer">
        <div class="skeleton-line price"></div>
        <div class="skeleton-line button"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.skeleton-card {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--border-color);
}

.skeleton-image {
  height: 200px;
  background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

.skeleton-content {
  padding: var(--spacing-md);
}

.skeleton-line {
  height: 12px;
  background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 6px;
  margin-bottom: 12px;
}

.skeleton-line.title {
  height: 20px;
  width: 80%;
}

.skeleton-line.text {
  width: 90%;
}

.skeleton-line.text.short {
  width: 60%;
}

.skeleton-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 24px;
}

.skeleton-line.price {
  height: 24px;
  width: 30%;
}

.skeleton-line.button {
  height: 32px;
  width: 40%;
  border-radius: var(--radius-full);
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

[data-theme="dark"] .skeleton-image,
[data-theme="dark"] .skeleton-line {
  background: linear-gradient(90deg, var(--gray-800) 25%, var(--gray-700) 50%, var(--gray-800) 75%);
  background-size: 200% 100%;
}
</style>
